<?php

namespace App;

use SilverStripe\Control\Director;
use SilverStripe\View\TemplateGlobalProvider;

class GlobalTemplateProvider implements TemplateGlobalProvider
{

    /**
     * Provides a global $IsDev variable to be used in templates
     */
    public static function get_template_global_variables(): array
    {
        return [
            'IsDev' => 'isDev',
        ];
    }

    /**
     * Checks whether we're in dev mode or not.
     */
    public static function isDev(): bool
    {
        return Director::isDev();
    }

}
