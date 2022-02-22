<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

use App\Pages\BusinessListingPage;
use App\Tracking\SiteTreeTrackingExtension;
use SilverStripe\CMS\Model\SiteTree;

/**
 * Decorator for mapping page data to generic link components
 */
class PageLinkDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'LinkTitle' => 'Title',
        'LinkHref' => 'Link',
    ];

    protected function produceData(?array $additionalProperties = null): array
    {
        /** @var SiteTree|SiteTreeTrackingExtension $dataObject */
        $dataObject = $this->getDataObject();

        if ($dataObject instanceof BusinessListingPage) {
            $additionalProperties = array_merge(
                $additionalProperties,
                [
                    'OperatorName' => $dataObject->BusinessKey,
                    'ListingID' => $dataObject->OID,
                ]
            );

            return parent::produceData($additionalProperties);
        }

        $additionalProperties = array_merge($additionalProperties, ['OperatorName' => '', 'ListingID' => '']);

        return parent::produceData($additionalProperties);
    }
}
