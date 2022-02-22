<?php

namespace App\Tracking\DataLayer;

use App\Extensions\UniqueIdExtension;
use App\Listing\TemplateIdProvider;
use Exception;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataObject;

/**
 * Provide a section ID field for listing blocks with a fallback
 *
 * @property DataObject|$this $owner
 */
class SectionIdExtension extends Extension
{
    /**
     * @return string|null
     * @throws Exception
     */
    public function getSectionID(): ?string
    {
        /** @var DataObject|UniqueIdExtension $owner */
        $owner = $this->owner;
        $sectionID = TemplateIdProvider::findSectionIdByClass($owner->ClassName);

        if ($sectionID !== null) {
            return $sectionID;
        }

        if ($owner->hasExtension(UniqueIdExtension::class)) {
            // Use our custom unique ID if available
            return $owner->getUniqueID();
        }

        // Fall back to core default
        return $owner->getUniqueKey();
    }
}
