<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

class BlockDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'ID' => 'getUniqueID',
    ];
}
