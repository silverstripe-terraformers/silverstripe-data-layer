<?php

namespace App\Tracking\DataLayer\DataComponentDecorators\Listing;

use App\Tracking\DataLayer\DataComponentDecorators\AbstractDecorator;

class PageInteractionDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'OperatorName' => 'BusinessKey',
        'ListingID' => 'BusinessOID',
    ];

    /*
     * Page interactions can't use the page id as that's too generic
     */
    protected function shouldUseId(): bool
    {
        return false;
    }
}
