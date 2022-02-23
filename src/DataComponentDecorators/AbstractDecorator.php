<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

use InvalidArgumentException;
use SebastianBergmann\CodeCoverage\Report\PHP;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\DataLayer\Config\ComponentDTO;
use SilverStripe\DataLayer\Config\Field;
use SilverStripe\DataLayer\Config\Manifest;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ValidationException;

/**
 * Extend this to implement specific decorators, they enable mapping a field name
 * to a value specifically. When creating a new decorator/component definition you'll
 * need to add it into @see DecoratorService which maps a component specification to
 * a decorator.
 */
abstract class AbstractDecorator
{

    use Configurable;
    use Injectable;

    // Used in `getFieldValue` for overriding a fields value
    public const VALUE_UNDEFINED = 'AbstractGenerator::VALUE_UNDEFINED';

    /**
     * @var string[]
     */
    private static $generic_values = [];

    /**
     * @var DataObject
     */
    private $dataObject;

    /**
     * @var string
     */
    private $componentKey;

    /**
     * @param string $componentKey
     * @param DataObject|null $instance
     */
    public function __construct(string $componentKey, ?DataObject $instance)
    {
        $this->dataObject = $instance;
        $this->componentKey = $componentKey;
    }

    /**
     * @return DataObject|null
     */
    public function getDataObject(): ?DataObject
    {
        return $this->dataObject;
    }

    /**
     * @return ComponentDTO|null
     * @throws ValidationException
     */
    public function getComponentConfig(): ?ComponentDTO
    {
        return Manifest::singleton()->getByKey($this->componentKey);
    }

    /**
     * @param array|null $additionalProperties
     * @return DBField
     * @throws ValidationException
     */
    public function getAttributes(?array $additionalProperties = null): DBField
    {
        $data = $this->produceData($additionalProperties);
        $attributes = [
            'data' => $data,
        ];

        if ($this->shouldUseId()) {
            if (array_key_exists('ID', $data)) {
                // ID is provided via a property
                $attributes['id'] = $data['ID'];
            } elseif ($this->getDataObject() !== null) {
                // ID can be taken from model
                $attributes['id'] = $this->getDataObject()->getUniqueKey();
            }
        }

        foreach ($this->getComponentConfig()->getInteractions() as $interaction) {
            $attributes[$interaction] = true;
        }

        $parts = [];

        foreach ($attributes as $key => $attribute) {
            // Make sure attribute encoding is applied in all cases as some data is managed by content authors
            if (is_array($attribute)) {
                $parts[] = sprintf(
                    "data-layer-%s='%s'",
                    $key,
                    Convert::raw2att(json_encode($attribute, JSON_FORCE_OBJECT))
                );

                continue;
            }

            $parts[] = sprintf('data-layer-%s="%s"', $key, Convert::raw2att($attribute));
        }

        return DBField::create_field('HTMLFragment', implode(' ', $parts));
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function castValue(string $type, $value)
    {
        if ($type === Field::TYPE_INTEGER) {
            return (int) $value;
        }

        return (string) $value;
    }

    /**
     * @param array|null $additionalProperties
     * @return array
     * @throws ValidationException
     */
    protected function produceData(?array $additionalProperties = null): array
    {
        $config = $this->getComponentConfig();
        $results = [];

        foreach ($config->getFields() as $field) {
            $key = $field->getKey();
            $type = $field->getType();
            $dataObject = $this->getDataObject();

            $value = $this->getFieldValue($config->getComponentKey(), $field, $dataObject);

            // Identifier types are expected to be provided via static configuration
            // These are never populated by other means
            if ($type === Field::TYPE_IDENTIFIER) {
                $results[$key] = $this->castValue(Field::TYPE_STRING, $field->getValue());

                continue;

            }

            // The value has been declared via the decorator
            if ($value !== self::VALUE_UNDEFINED) {
                $results[$key] = $this->castValue($type, $value);

                continue;
            }

            if ($additionalProperties && array_key_exists($key, $additionalProperties)) {
                // Value is provided as an additional property
                $results[$key] = $this->castValue($type, $additionalProperties[$key]);

                continue;
            }

            // If the value has been specifically mapped
            $mappedValue = $this->getValueFromMapping($key, $dataObject);

            if ($mappedValue !== self::VALUE_UNDEFINED) {
                $results[$key] = $this->castValue($type, $mappedValue);

                continue;
            }

            // Check if the value is a field on the Data Object
            if ($dataObject && $dataObject->hasField($key)) {
                // We've got a direct field (or a field populated by other means like an extension or a get method)
                $results[$key] = $this->castValue($type, $dataObject->{$key});

                continue;
            }

            if ($field->getValue() !== null) {
                // We've got a hardcoded default value, no need
                // to try and fetch this from the model
                $results[$key] = $this->castValue($type, $field->getValue());

                continue;
            }

            // Finally throw
            throw new InvalidArgumentException(sprintf(
                'Unable to find %s field on %s class for %s',
                $key,
                $dataObject ? get_class($dataObject) : 'null',
                $this->getComponentConfig()->getComponentKey(),
            ));
        }

        return $results;
    }

    /**
     * Return `static::VALUE_UNDEFINED` if you would like to mark the value as undefined
     *
     * @return mixed|string|null
     */
    protected function getFieldValue(string $componentKey, Field $field, ?DataObject $dataObject)
    {
        return self::VALUE_UNDEFINED;
    }

    /*
     * Disable adding an ID if you've got components without IDs
     */
    protected function shouldUseId(): bool
    {
        return true;
    }

    /*
     * Represents the combination of generic values and component values
     * for the specified component
     */
    protected function getMapping(?string $componentKey = null): array
    {
        $genericValues = $this->config()->get('generic_values');

        if (!$componentKey) {
            return $genericValues;
        }

        $componentMapping = $this->config()->get('component_values');

        // Do we have mapping for our component?
        if (!is_array($componentMapping) || !array_key_exists($componentKey, $componentMapping)) {
            // The answer is no, so we just use the generic mapping
            return $genericValues;
        }

        // Combine generic values with our component specific values
        return array_merge($genericValues, $componentMapping[$componentKey]);
    }

    /**
     * Based on a key, get the value for the mapping
     *
     * @param string $key
     * @param DataObject|null $dataObject
     * @return mixed|DataObject|DBField|null
     * @throws ValidationException
     */
    protected function getValueFromMapping(string $key, ?DataObject $dataObject)
    {
        $mapping = $this->getMapping($this->getComponentConfig()->getComponentKey());

        if (!array_key_exists($key, $mapping) || !$dataObject) {
            return self::VALUE_UNDEFINED;
        }

        $valueKey = $mapping[$key];
        // We've got a field that has been mapped
        $methods = explode('.', $valueKey);
        $result = $dataObject;

        while (count($methods) > 0) {
            $method = array_shift($methods);

            if ($result->hasMethod($method)) {
                $result = $dataObject->{$method}();

                continue;
            }

            if ($result->hasField($method)) {
                // We've got a direct field (or a field populated by other means like an extension or a get method)
                $result = $result->{$method};

                continue;
            }

            throw new InvalidArgumentException(sprintf(
                'Unable to find mapped field %s on %s class for %s',
                $method,
                get_class($result),
                $valueKey,
            ));
        }

        if ($result instanceof DataObject) {
            throw new InvalidArgumentException(sprintf(
                'Mapping "%s" returned a data object (%s)',
                $valueKey,
                get_class($result)
            ));
        }

        return $result;
    }
}
