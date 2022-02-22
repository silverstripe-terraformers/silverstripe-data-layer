<?php

/**
 * Meta File to extend PHPStorm IDE built-in "code awareness"
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html
 *
 * feel free to extend this file with extra cases frequently used within Silverstripe projects
 * with minimal modification, this file should be possible just copy-paste to existing projects
 */

namespace PHPSTORM_META;

override(\SilverStripe\Dev\SapphireTest::objFromFixture(0, 1), type(0));
override(\SilverStripe\Core\Injector\Injector::create(0, 1), type(0));
override(\SilverStripe\Core\Injector\Injector::createWithArgs(0, 1), type(0));
