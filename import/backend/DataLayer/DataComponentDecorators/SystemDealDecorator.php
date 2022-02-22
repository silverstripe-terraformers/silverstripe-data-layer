<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

class SystemDealDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'LinkHref' => 'ReadMoreLink.URL',
        'ListingID' => 'BusinessListingPage.OID',
        'OperatorName' => 'ReadMoreLink.OperatorName',
        'DealCampaignKeys' => 'getCampaignTrackingKeys',
        'DealTitle' => 'getTrackDealTitle',
        'DealFullName' => 'getTrackDealFullName',
        'DealType' => 'getPriceTypeFriendly',
    ];

    protected function shouldUseId(): bool
    {
        return false;
    }
}
