<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

/**
 * Basic decorator for models
 * Mostly useful for quickly outputting some data into template for testing purposes
 */
class DataObjectDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $field_map = [
        'ID' => 'getUniqueKey',
        'Title' => 'Title',
    ];
}
