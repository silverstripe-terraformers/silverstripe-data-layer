<?php

namespace SilverStripe\DataLayer\Config;

use SilverStripe\Core\Injector\Injectable;

/**
 * This is a representation of the component from a specification
 * this component is cached in-memory via @see Manifest
 */
class ComponentDTO
{

    use Injectable;

    /**
     * @var string
     */
    private $componentKey;

    /**
     * @var Field[]
     */
    private $fieldsSpecifications = [];

    /**
     * @var Field[]|null
     */
    private $fields = null;

    /**
     * @var string|null
     */
    private $extends = null;

    /**
     * @var string[]
     */
    private $interactions = [];

    /**
     * @param string $componentKey
     * @param array $specification
     */
    public function __construct(string $componentKey, array $specification)
    {
        $this->componentKey = $componentKey;

        if (array_key_exists('extends', $specification)) {
            $this->extends = $specification['extends'];
        }

        if (array_key_exists('interactions', $specification)) {
            $this->interactions = $specification['interactions'];
        }

        if (!array_key_exists('fields', $specification)) {
            return;
        }

        foreach ($specification['fields'] as $field) {
            $this->fieldsSpecifications[] = [
                $field['key'],
                $field['type'],
                $field['value'] ?? null,
            ];
        }
    }

    /**
     * Adds inherited field defined in the parent component
     *
     * @param ComponentDTO $component
     */
    public function buildExtensions(ComponentDTO $component): void
    {
        if (!$this->extends) {
            return;
        }

        $fields = $this->getFields();

        foreach ($component->getFields() as $fieldId => $field) {
            if (array_key_exists($fieldId, $fields)) {
                // We've already overwritten this field - bail out
                continue;
            }

            $this->fields[$fieldId] = $field;
        }

        foreach ($component->getInteractions() as $interaction) {
            $this->interactions[] = $interaction;
        }

        $this->interactions = array_unique($this->interactions);
    }

    /**
     * @return string
     */
    public function getComponentKey(): string
    {
        return $this->componentKey;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        // Initialise field objects
        if ($this->fields === null) {
            $this->fields = [];

            foreach ($this->fieldsSpecifications as $data) {
                [$key, $type, $value] = $data;

                $this->fields[$key] = Field::create(
                    $key,
                    $type,
                    $value ?? null
                );
            }
        }

        return $this->fields;
    }

    /**
     * @return string[]
     */
    public function getInteractions(): array
    {
        return $this->interactions;
    }

    /**
     * @return string|null
     */
    public function getExtends(): ?string
    {
        return $this->extends;
    }
}
