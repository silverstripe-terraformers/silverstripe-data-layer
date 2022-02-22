<?php

namespace App\Tracking\DataLayer\DataComponentDecorators\Listing;

use App\Tracking\DataLayer\DataComponentDecorators\AbstractDecorator;

class SectionDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'ID' => 'getSectionID',
    ];
}
