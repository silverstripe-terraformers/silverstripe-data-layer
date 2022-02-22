<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

use InvalidArgumentException;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\DataLayer\Config\ComponentDTO;
use SilverStripe\DataLayer\Config\Field;
use SilverStripe\DataLayer\Config\Manifest;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;

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
    private static $generic_values = [
        'OperatorName' => 'BusinessKey',
    ];

    /**
     * @var DataObject
     */
    private $dataObject;

    /**
     * @var string
     */
    private $componentKey;

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

    public function getComponentConfig(): ?ComponentDTO
    {
        return Manifest::singleton()->getByKey($this->componentKey);
    }

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

    protected function produceData(?array $additionalProperties = null): array
    {
        $config = $this->getComponentConfig();
        $results = [];

        foreach ($config->getFields() as $field) {
            $key = $field->getKey();
            $dataObject = $this->getDataObject();

            $value = $this->getFieldValue($config->getComponentKey(), $field, $dataObject);

            // The value has been declared via the generator
            if ($value !== self::VALUE_UNDEFINED) {
                $results[$key] = $value;

                continue;
            }

            if ($field->getValue() !== null) {
                // We've got a hardcoded default value, no need
                // to try and fetch this from the model
                $results[$key] = $field->getValue();

                continue;
            }

            if ($additionalProperties && array_key_exists($key, $additionalProperties)) {
                // Value is provided as an additional property
                $results[$key] = $additionalProperties[$key];

                continue;
            }

            // If the value has been specifically mapped
            $mappedValue = $this->getValueFromMapping($key, $dataObject);

            if ($mappedValue !== self::VALUE_UNDEFINED) {
                $results[$key] = $mappedValue;

                continue;
            }

            // Check if the value is a field on the Data Object
            if ($dataObject && $dataObject->hasField($key)) {
                // We've got a direct field (or a field populated by other means like an extension or a get method)
                $results[$key] = $dataObject->{$key};

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
    private function getMapping(?string $componentKey = null): array
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
     * @param DataObject $dataObject
     * @return mixed|DataObject|DBField|null
     */
    private function getValueFromMapping(string $key, ?DataObject $dataObject)
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
