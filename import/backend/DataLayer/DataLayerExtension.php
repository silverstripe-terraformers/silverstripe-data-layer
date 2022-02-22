<?php

namespace App\Tracking\DataLayer;

use SilverStripe\Core\Extension;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DataObject;

/**
 * We can't expose `$Me` to the global template function `DataLayerAttributes`
 * Therefore we have this stop gap to handle those scenarios. It's not perfect,
 * but it gets the job done (just like me).
 *
 * If we need to enable this for objects such as ArrayData then we should look
 * at creating a ViewableData extension (this is not documented and there's no
 * examples, so at the time of creation I've opted for the well-supported route)
 *
 *  * Future routes:
 *  - Add support for an ID param
 *    - This would allow us to have two of the same components and separate them with
 *      an ID which is coded into either the template or a nonce
 *
 * @property DataObject|$this $owner
 */
class DataLayerExtension extends Extension
{
    public function DataLayerAttributes(?string $componentKey = null): ?DataLayerField
    {
        // You never know how bad the data you'll get is
        if (!$componentKey) {
            Debug::message('$DataLayerAttributes called without a Component ID');

            return null;
        }

        return DataLayerField::create('DataLayerField')
            ->setComponentKey($componentKey)
            ->setModel($this->owner);
    }
}
