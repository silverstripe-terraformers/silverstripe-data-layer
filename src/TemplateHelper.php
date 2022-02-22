<?php

namespace SilverStripe\DataLayer;

use SilverStripe\Dev\Debug;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * The current expectation is to use a component ID to get a component specification
 * and to pass through a data object to add values to the specification.
 */
class TemplateHelper implements TemplateGlobalProvider
{
    public static function get_template_global_variables(): array
    {
        return [
            'GenericDataLayerAttributes' => ['method' => 'getGenericDataLayerAttributes', 'casting' => 'HTMLText'],
            'CastToNumeric' => ['method' => 'castToNumeric', 'casting' => 'Int'],
        ];
    }

    /**
     * @param string|null $componentKey
     * @return DataLayerField|null
     */
    public static function getGenericDataLayerAttributes(?string $componentKey): ?DataLayerField
    {
        // You never know how bad the data you'll get is
        if (!$componentKey) {
            Debug::message('$DataLayerAttributes called without a Component ID');

            return null;
        }

        return DataLayerField::create('DataLayerField')->setComponentKey($componentKey);
    }

    /**
     * Cast a value to a numeric string
     *
     * @param mixed $value
     * @return string
     */
    public static function castToNumeric($value): string
    {
        $value = (int) $value;

        return (string) $value;
    }
}
