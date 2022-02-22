<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

class ModelDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'ID' => 'getUniqueKey',
    ];
}
