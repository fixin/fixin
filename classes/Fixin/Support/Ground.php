<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Ground extends DoNotCreate
{
    protected const
        DEBUG_TEXT_TEMPLATE = '<div style="font-family: monospace; white-space: pre; color: #000; line-height: 1.05">%s</div>';

    /**
     * Display debug text for environment
     */
    public static function debugText(string $html): string
    {
        return static::isConsole() ? htmlspecialchars_decode(strip_tags($html)) : sprintf(static::DEBUG_TEXT_TEMPLATE, $html);
    }

    /**
     * Check if running in CLI mode
     */
    public static function isConsole(): bool
    {
        static $isConsole = null;

        return $isConsole ?? ($isConsole = PHP_SAPI === 'cli');
    }
}
