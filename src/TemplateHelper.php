<?php

namespace SilverStripe\DataLayer;

use SilverStripe\ORM\ValidationException;
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
     * Get Data Layer attributes for a generic component
     * This covers the cases where there is no data source and all data is populated via a template
     *
     * @param string|null $componentKey
     * @return DataLayerField
     * @throws ValidationException
     */
    public static function getGenericDataLayerAttributes(?string $componentKey): DataLayerField
    {
        if (!$componentKey) {
            throw new ValidationException('$GenericDataLayerAttributes called without a component key');
        }

        return DataLayerField::create(sprintf('data-layer-field-generic-%s', $componentKey))
            ->setComponentKey($componentKey);
    }
}
