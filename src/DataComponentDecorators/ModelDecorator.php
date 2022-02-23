<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

/**
 * Basic decorator for models
 * Provides a mapping for ID field
 */
class ModelDecorator extends AbstractDecorator
{
    /**
     * @var string[]
     */
    private static $generic_values = [
        'ID' => 'getUniqueKey',
    ];
}
