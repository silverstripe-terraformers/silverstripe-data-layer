<?php

namespace SilverStripe\DataLayer\Config;

use SilverStripe\Core\Injector\Injectable;

/**
 * This represents a single field on a component
 * The value is optional and null by default, it's used to represent
 * the default value in component specifications
 */
class Field
{

    use Injectable;

    /**
     * Basic type - string
     */
    public const TYPE_STRING = 'String';

    /**
     * Basic type - integer
     */
    public const TYPE_INTEGER = 'Integer';

    /**
     * Special type - identifier
     * This is a string populated from the specifications
     * Does not support populating the value from other sources
     */
    public const TYPE_IDENTIFIER = 'Identifier';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @param string $key
     * @param string $type
     * @param mixed|null $value
     */
    public function __construct(string $key, string $type, $value = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
