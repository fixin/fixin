<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class Ground extends DoNotCreate
{
    protected const
        DEBUG_HTML_TEMPLATE = '<div style="font-family: monospace; white-space: pre; color: #000; line-height: 1.05">%s</div>';

    /**
     * Check if running in CLI mode
     *
     * @return bool
     */
    public static function isConsole(): bool
    {
        static $isConsole = null;

        return $isConsole ?? ($isConsole = PHP_SAPI === 'cli');
    }

    /**
     * Debug text block for environment
     *
     * @param string $html
     * @return string
     */
    public static function toDebugBlock(string $html): string
    {
        return static::isConsole() ? htmlspecialchars_decode(strip_tags($html)) : sprintf(static::DEBUG_HTML_TEMPLATE, $html);
    }

    /**
     * Debug text for environment
     *
     * @param string $html
     * @return string
     */
    public static function toDebugText(string $html): string
    {
        return static::isConsole() ? htmlspecialchars_decode(strip_tags($html)) : $html;
    }
}
