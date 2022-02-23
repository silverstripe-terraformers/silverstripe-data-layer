<?php

namespace SilverStripe\DataLayer\Tests\DataLayerField;

use SilverStripe\DataLayer\Config\Field;
use SilverStripe\DataLayer\DataComponentDecorators\AbstractDecorator;
use SilverStripe\Dev\TestOnly;

class MockDecorator extends AbstractDecorator implements TestOnly
{
    protected function getFieldValue(string $componentKey, Field $field): string
    {
        return 'Mock value';
    }
}
