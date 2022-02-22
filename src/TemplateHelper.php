<?php

namespace SilverStripe\DataLayer;

use SilverStripe\Dev\Debug;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Template helper methods to provide easy access to data layer methods
 */
class TemplateHelper implements TemplateGlobalProvider
{
    public static function get_template_global_variables(): array
    {
        return [
            'GenericDataLayerAttributes' => ['method' => 'getGenericDataLayerAttributes', 'casting' => 'HTMLText'],
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

        return DataLayerField::create('data-layer-field-generic')->setComponentKey($componentKey);
    }
}
