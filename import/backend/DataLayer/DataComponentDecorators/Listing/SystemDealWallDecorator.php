<?php

namespace App\Tracking\DataLayer\DataComponentDecorators\Listing;

class SystemDealWallDecorator extends SectionDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'Location' => 'getTrackPagePosition',
    ];
}
