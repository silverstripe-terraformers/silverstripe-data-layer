<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

use App\Tracking\SiteTreeTrackingExtension;
use SilverStripe\CMS\Model\SiteTree;

class PageTrackingDataDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'ID' => 'getUniqueID',
    ];

    protected function produceData(?array $additionalProperties = null): array
    {
        $data = parent::produceData($additionalProperties);

        /** @var SiteTree|SiteTreeTrackingExtension $dataObject */
        $dataObject = $this->getDataObject();

        if (!$dataObject instanceof SiteTree) {
            return $data;
        }

        return array_merge_recursive($data, json_decode($dataObject->getPageTrackingData(), true));
    }
}
