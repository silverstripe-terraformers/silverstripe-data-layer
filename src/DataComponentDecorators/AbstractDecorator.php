<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

use Exception;
use InvalidArgumentException;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\DataLayer\Config\ComponentDTO;
use SilverStripe\DataLayer\Config\Field;
use SilverStripe\DataLayer\Config\Manifest;
use SilverStripe\DataLayer\DataLayerExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\View\ViewableData;

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
     * Mapping of source object fields and component fields
     * Format:
     * 'component_field' => 'source_object_field'
     *
     * @var string[]
     * @config
     */
    private static $field_map = [];

    /**
     * @var ViewableData
     */
    private $sourceObject;

    /**
     * @var string
     */
    private $componentKey;

    /**
     * @param string $componentKey
     * @param ViewableData|null $instance
     */
    public function __construct(string $componentKey, ?ViewableData $instance)
    {
        $this->sourceObject = $instance;
        $this->componentKey = $componentKey;
    }

    /**
     * @return ViewableData|DataLayerExtension|null
     */
    public function getSourceObject(): ?ViewableData
    {
        return $this->sourceObject;
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
     * @throws Exception
     */
    public function getAttributes(?array $additionalProperties = null): DBField
    {
        $data = $this->produceData($additionalProperties);
        $attributes = [
            'data' => $data,
        ];

        // extract ID and copy it over to a separate attribute, so it could be processed easily by events
        if (array_key_exists('ID', $data)) {
            $attributes['id'] = $data['ID'];
        }

        foreach ($this->getComponentConfig()->getInteractions() as $interaction) {
            $attributes[$interaction] = true;
        }

        $parts = [];

        // Make sure attribute encoding is applied in all cases as some data may be managed by content authors
        foreach ($attributes as $key => $attribute) {
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
     * Create tracking data as per component specification
     * The priority order of field resolution is as follows:
     *
     * 1 - identifier field type
     * 2 - additional properties
     * 3 - directly from decorator
     * 4 - from field mapping
     * 5 - from source object
     * 6 - from default value
     *
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
            $sourceObject = $this->getSourceObject();

            $value = $this->getFieldValue($config->getComponentKey(), $field);

            // Identifier types are expected to be provided via static configuration
            // These are never populated by other means
            if ($type === Field::TYPE_IDENTIFIER) {
                $results[$key] = $this->castValue(Field::TYPE_STRING, $field->getValue());

                continue;
            }

            if ($additionalProperties && array_key_exists($key, $additionalProperties)) {
                // Value is provided as an additional property
                $results[$key] = $this->castValue($type, $additionalProperties[$key]);

                continue;
            }

            // The value has been declared via the decorator
            if ($value !== self::VALUE_UNDEFINED) {
                $results[$key] = $this->castValue($type, $value);

                continue;
            }

            // If the value has been specifically mapped
            $mappedValue = $this->getValueFromMapping($key);

            if ($mappedValue !== self::VALUE_UNDEFINED) {
                $results[$key] = $this->castValue($type, $mappedValue);

                continue;
            }

            // Check if the value is a field on the Data Object
            if ($sourceObject && $sourceObject->hasField($key)) {
                // We've got a direct field (or a field populated by other means like an extension or a get method)
                $results[$key] = $this->castValue($type, $sourceObject->{$key});

                continue;
            }

            if ($field->getValue() !== null) {
                // We've got a hardcoded default value, no need
                // to try and fetch this from the model
                $results[$key] = $this->castValue($type, $field->getValue());

                continue;
            }

            // Finally throw
            throw new ValidationException(
                sprintf(
                    'Unable to find %s field on %s class for %s',
                    $key,
                    $sourceObject ? get_class($sourceObject) : 'null',
                    $this->getComponentConfig()->getComponentKey(),
                )
            );
        }

        return $results;
    }

    /**
     * Return `static::VALUE_UNDEFINED` if you would like to mark the value as undefined
     *
     * @param string $componentKey
     * @param Field $field
     * @return mixed
     */
    protected function getFieldValue(string $componentKey, Field $field)
    {
        return self::VALUE_UNDEFINED;
    }

    /**
     * Get field mapping based on the specified decorator map
     *
     * @param string $componentKey
     * @return array
     */
    protected function getMapping(string $componentKey): array
    {
        $genericMapping = (array) $this->config()->get('field_map');
        $sourceObject = $this->getSourceObject();

        if ($sourceObject === null) {
            // We don't have any source object available - use generic mapping
            return $genericMapping;
        }

        $decorators = (array) $sourceObject->config()->get('data_layer_decorators');

        if (!array_key_exists($componentKey, $decorators)) {
            // We don't have any source object specific mapping - use generic mapping
            return $genericMapping;
        }

        $specificMapping = (array) $decorators[$componentKey];

        // Merge mappings -  specific mapping overrides the generic mapping
        return $specificMapping + $genericMapping;
    }

    /**
     * Get the value for the mapping based on specified key
     *
     * @param string $key
     * @return mixed|ViewableData|DBField|null
     * @throws ValidationException
     */
    protected function getValueFromMapping(string $key)
    {
        $sourceObject = $this->getSourceObject();
        $mapping = $this->getMapping($this->getComponentConfig()->getComponentKey());

        if (!array_key_exists($key, $mapping) || !$sourceObject) {
            return self::VALUE_UNDEFINED;
        }

        $valueKey = $mapping[$key];
        // We've got a field that has been mapped
        $methods = explode('.', $valueKey);
        $result = $sourceObject;

        while ($method = array_shift($methods)) {
            if ($result->hasMethod($method)) {
                $result = $sourceObject->{$method}();

                continue;
            }

            if ($result->hasField($method)) {
                // We've got a direct field (or a field populated by other means like an extension or a get method)
                $result = $result->{$method};

                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Unable to find mapped field %s on %s class for %s',
                    $method,
                    get_class($result),
                    $valueKey,
                )
            );
        }

        if ($result instanceof ViewableData) {
            throw new InvalidArgumentException(
                sprintf(
                    'Mapping "%s" returned a data object (%s)',
                    $valueKey,
                    get_class($result)
                )
            );
        }

        return $result;
    }
}
