<?php

namespace SilverStripe\DataLayer\Tests\DataLayerField;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

/**
 * @property string $Title
 * @property int $Location
 */
class BasicModel extends DataObject implements TestOnly
{
    /**
     * @var string
     */
    private static $table_name = 'DataLayerTest_BasicModel';

    /**
     * @var string[]
     */
    private static $db = [
        'Title' => 'Varchar',
        'Location' => 'Int',
    ];

    public function getTestRelation(): self
    {
        return $this;
    }
}
