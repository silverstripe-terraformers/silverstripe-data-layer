<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

class LinkDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'LinkTitle' => 'Title',
        'LinkHref' => 'getLinkURL',
        'OperatorName' => 'getDataLayerOperatorName',
        'ListingID' => 'getDataLayerListingID',
    ];

    protected function shouldUseId(): bool
    {
        return false;
    }
}
