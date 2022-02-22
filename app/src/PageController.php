<?php

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Core\Environment;

class PageController extends ContentController
{

    /**
     * An array of actions that can be accessed via a request. Each array element should be an action name, and the
     * permissions or conditions required to allow the user to access it.
     *
     * <code>
     * array (
     *     'action', // anyone can access this action
     *     'action' => true, // same as above
     *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
     *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
     * );
     * </code>
     */
    private static array $allowed_actions = [];

    /**
     * Allow the BetterNavigator debug module to be shown/hidden from output.
     *
     * @see https://github.com/jonom/silverstripe-betternavigator
     * @return bool
     */
    public function showBetterNavigator(): bool
    {
        // A user-defined setting
        return !Environment::getEnv('HIDE_BETTER_NAVIGATOR');
    }

    /**
     * You can include any CSS or JS required by your project after parent::init().
     * See: http://doc.silverstripe.org/framework/en/reference/requirements
     */
    protected function init() // phpcs:ignore SlevomatCodingStandard.TypeHints
    {
        parent::init();
    }

}
