<?php

namespace App\Tracking\DataLayer\Config;

use SilverStripe\Core\Injector\Injectable;

/**
 * This is a representation of the component from a specification
 * It's a pretty dumb DTO with a little smarts around resolving its dependencies
 * from a @see Manifest
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
    private $fields = [];

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
            $this->fields[$field['key']] = Field::create(
                $field['key'],
                $field['type'],
                $field['value'] ?? null
            );
        }
    }

    /*
     * Called after the manifest has added all components DTOs to allow
     * us to extend component definitions from the manifest and add their
     * fields to our fields
     */
    public function buildExtensions(ComponentDTO $component): void
    {
        if (!$this->extends) {
            return;
        }

        foreach ($component->getFields() as $fieldId => $field) {
            if (array_key_exists($fieldId, $this->fields)) {
                // We've already overwritten this field
                continue;
            }

            $this->fields[$fieldId] = $field;
        }

        foreach ($component->getInteractions() as $interaction) {
            $this->interactions[] = $interaction;
        }

        $this->interactions = array_unique($this->interactions);
    }

    public function getComponentKey(): string
    {
        return $this->componentKey;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getInteractions(): array
    {
        return $this->interactions;
    }

    public function getExtends(): ?string
    {
        return $this->extends;
    }
}
