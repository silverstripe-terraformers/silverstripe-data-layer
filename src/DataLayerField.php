<?php

namespace SilverStripe\DataLayer;

use Serializable;
use SilverStripe\DataLayer\DataComponentDecorators\DecoratorService;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationException;

/**
 * This field represents unprocessed state of @see DataLayerExtension::DataLayerAttributes()
 * which is still open to new changes (adding of new properties)
 * Note that this field needs to be transformed into a plain field in case we want it to be serialised into data cache
 * by calling getFieldForTemplate() method
 */
class DataLayerField extends DBHTMLText implements Serializable
{
    /**
     * @var DataObject|null
     */
    protected $model = null;

    /**
     * @var string|null
     */
    protected $componentKey = null;

    /**
     * @var array
     */
    protected $extraProperties = [];

    public function setModel(?DataObject $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setComponentKey(?string $componentKey): self
    {
        $this->componentKey = $componentKey;

        return $this;
    }

    public function addExtraProperties(array $properties): self
    {
        foreach ($properties as $key => $value) {
            $this->extraProperties[$key] = $value;
        }

        return $this;
    }

    public function getFieldForTemplate(): ?DBField
    {
        return DecoratorService::singleton()->process(
            DecoratorService::TYPE_ATTRIBUTES,
            $this->componentKey,
            $this->model,
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
