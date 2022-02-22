<?php

namespace App\View;

use SilverStripe\View\Shortcodes\EmbedShortcodeProvider;

class CustomEmbedShortcodeProvider extends EmbedShortcodeProvider
{

    /**
     * Define a default video width based on page dimensions
     */
    private const DEFAULT_WIDTH = 1280;

    /**
     * Define a default video height based on default page width dimensions
     */
    private const DEFAULT_HEIGHT = 720;

    /**
     * Used to ensure videos are responsive
     *
     * @param int $width The width of the video defined by the user or using the default value
     * @param int $height The height of the video defined by the user or using the default value
     * @return float
     */
    public static function calculateRatioPadding(int $width, int $height): float
    {
        return round($height / ($width / 100), 2);
    }

    /**
     * Handle the shortcode call, setting defaults for video dimensions
     * and adding the padding ratio
     *
     * @inheritDoc
     */
    public static function handle_shortcode($arguments, $content, $parser, $shortcode, $extra = array()): string
    {
        // Ensure we define a height and width
        $arguments['width'] ??= self::DEFAULT_WIDTH;
        $arguments['height'] ??= self::DEFAULT_HEIGHT;
        $arguments['ratioPadding'] = self::calculateRatioPadding((int)$arguments['width'], (int)$arguments['height']);

        return parent::handle_shortcode($arguments, $content, $parser, $shortcode, $extra);
    }

}
