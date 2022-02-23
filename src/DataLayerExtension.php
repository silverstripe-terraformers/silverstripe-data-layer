<?php

namespace SilverStripe\DataLayer;

use Exception;
use SilverStripe\Core\Extension;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DataObject;

/**
 * Expose a @see DataLayerField on every model to allow easier template decoration
 *
 * @method  DataObject|$this getOwner()
 */
class DataLayerExtension extends Extension
{
    /**
     * Template method to be used to produce tracking data attributes
     *
     * @param string|null $componentKey
     * @return DataLayerField|null
     * @throws Exception
     */
    public function DataLayerAttributes(?string $componentKey = null): ?DataLayerField
    {
        // You never know how bad the data you'll get is
        if (!$componentKey) {
            Debug::message('$DataLayerAttributes called without a Component ID');

            return null;
        }

        $owner = $this->getOwner();

        return DataLayerField::create('data-layer-field-' . $owner->getUniqueKey())
            ->setComponentKey($componentKey)
            ->setModel($owner);
    }
}
