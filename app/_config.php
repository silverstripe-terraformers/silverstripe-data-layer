<?php

use App\View\CustomEmbedShortcodeProvider;
use SilverStripe\View\Parsers\ShortcodeParser;

// Register our custom embed shortcode handler
ShortcodeParser::get('default')
    ->register('embed', [CustomEmbedShortcodeProvider::class, 'handle_shortcode']);
