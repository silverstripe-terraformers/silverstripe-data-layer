<?php

namespace App\Tracking\DataLayer;

use App\Extensions\Linkable\LinkableCustomFieldsExtension;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Core\Extension;

/**
 * @property Link|$this|LinkableCustomFieldsExtension $owner
 */
class DataLayerLinkExtension extends Extension
{
    /**
     * Links do not stored ListingIDs, so we need a default value
     * Template context which has ListingID should add it via @see DataLayerField::AddProp()
     *
     * @return string
     */
    public function getDataLayerListingID(): string
    {
        return '';
    }

    /**
     * Format operator name to needed format
     *
     * @return string
     */
    public function getDataLayerOperatorName(): string
    {
        return (string) $this->owner->OperatorName;
    }
}
