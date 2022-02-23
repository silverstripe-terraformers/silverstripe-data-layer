<?php

namespace SilverStripe\DataLayer;

use Exception;
use SilverStripe\Core\Extension;
use SilverStripe\DataLayer\DataComponentDecorators\DecoratorService;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;
use SilverStripe\View\ViewableData;

/**
 * Expose a @see DataLayerField on every object to allow easier template decoration
 *
 * @method ViewableData|$this getOwner()
 */
class DataLayerExtension extends Extension
{
    /**
     * Mapping of objects and their decorators
     * One object can act as a data source for multiple components, hence it can contain multiple mappings
     * This mapping is meant to cover decorators that have standard source (can be mapped to object)
     * For decorator / component mapping use @see DecoratorService::$decorators
     *
     * Mapping format:
     * Option 1: ['component_key' => 'decorator_class_name']
     * Option 2: ['component_key' => ['component_field' => 'source_object_field']]
     *
     * @var array
     * @config
     */
    private static $data_layer_decorators = [];

    /**
     * Template method to be used to produce tracking data attributes
     *
     * @param string|null $componentKey
     * @return DataLayerField
     * @throws ValidationException
     * @throws Exception
     */
    public function DataLayerAttributes(?string $componentKey = null): DataLayerField
    {
        // You never know how bad the data you'll get is
        if (!$componentKey) {
            throw new ValidationException('$DataLayerAttributes called without a component key');
        }

        $owner = $this->getOwner();

        return DataLayerField::create('data-layer-field-' . $owner->getGloballyUniqueID())
            ->setComponentKey($componentKey)
            ->setSourceObject($owner);
    }

    /**
     * Provide a globally unique ID
     *
     * @return string
     * @throws Exception
     */
    public function getGloballyUniqueID(): string
    {
        $owner = $this->getOwner();

        if ($owner instanceof DataObject) {
            // Data object has its own unique key feature
            return $owner->getUniqueKey();
        }

        // Fall back on a random hash
        return bin2hex(random_bytes(16));
    }
}
