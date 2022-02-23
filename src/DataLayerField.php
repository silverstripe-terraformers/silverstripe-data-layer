<?php

namespace SilverStripe\DataLayer;

use Serializable;
use SilverStripe\DataLayer\DataComponentDecorators\DecoratorService;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationException;
use SilverStripe\View\ViewableData;

/**
 * This field represents mutable state of @see DataLayerExtension::DataLayerAttributes()
 * Note that this field needs to be transformed into an immutable field in case we want it
 * to be serialised into data cache by calling @see DataLayerField::getFieldForTemplate()
 */
class DataLayerField extends DBHTMLText implements Serializable
{
    /**
     * @var ViewableData|null
     */
    protected $sourceObject = null;

    /**
     * @var string|null
     */
    protected $componentKey = null;

    /**
     * @var array
     */
    protected $extraProperties = [];

    /**
     * @param ViewableData|null $sourceObject
     * @return $this
     */
    public function setSourceObject(?ViewableData $sourceObject): self
    {
        $this->sourceObject = $sourceObject;

        return $this;
    }

    /**
     * @param string|null $componentKey
     * @return $this
     */
    public function setComponentKey(?string $componentKey): self
    {
        $this->componentKey = $componentKey;

        return $this;
    }

    /**
     * Add additional properties to the data set of this field
     * Useful when overriding default values
     *
     * @param array $properties
     * @return $this
     */
    public function addExtraProperties(array $properties): self
    {
        foreach ($properties as $key => $value) {
            $this->extraProperties[$key] = $value;
        }

        return $this;
    }

    /**
     * Get an immutable version of this field
     * Useful for field serialisation
     *
     * @return DBField|null
     * @throws ValidationException
     */
    public function getFieldForTemplate(): ?DBField
    {
        return DecoratorService::singleton()->process(
            DecoratorService::TYPE_ATTRIBUTES,
            $this->componentKey,
            $this->sourceObject,
            $this->extraProperties
        );
    }

    /**
     * Template method (can be chained)
     * Note that adding an ID property will also add a separate ID attribute
     * Examples:
     * .AddProp('test', 123) will result in data-layer-data JSON containing property `test` with value 123
     * .AddProp('ID', 123) will result in data-layer-data JSON containing property `ID` with value 123
     * and data-layer-id attribute having value 123
     * Passing multiple values will glue them together into a single value
     * .AddProp('test', 'abc', 'def') is the same as .AddProp('test', 'abcdef')
     *
     * @param string $key
     * @param mixed ...$values
     * @return $this
     */
    public function AddProp(string $key, ...$values): self
    {
        $this->addExtraProperties([$key => implode('', $values)]);

        return $this;
    }

    /**
     * @return mixed|string|null
     * @throws ValidationException
     */
    public function forTemplate()
    {
        $field = $this->getFieldForTemplate();

        if ($field instanceof DBField) {
            return $field->forTemplate();
        }

        return null;
    }

    /**
     * @return string|null
     * @throws ValidationException
     */
    public function serialize(): ?string
    {
        throw new ValidationException(
            'This field should never be serialised directly, '
            . 'use getFieldForTemplate() to get a plain field for serialisation'
        );
    }

    /**
     * @param string|mixed $data
     * @throws ValidationException
     */
    public function unserialize($data): void
    {
        throw new ValidationException(
            'This field should never be serialised directly, '
            . 'use getFieldForTemplate() to get a plain field for serialisation'
        );
    }
}
