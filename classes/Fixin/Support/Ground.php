<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Ground extends DoNotCreate {

    /**
     * Display debug text for environment
     *
     * @param string $html
     * @return string
     */
    public static function debugText(string $html) {
        return static::isConsole()
        ? htmlspecialchars_decode(strip_tags($html))
        : '<div style="font-family: monospace; white-space: pre; color: #000; line-height: 1.05">' . $html . '</div>';
    }

    /**
     * Check if running in CLI mode
     *
     * @return boolean
     */
    public static function isConsole() {
        static $isConsole = null;

        return $isConsole ?? ($isConsole = PHP_SAPI === 'cli');
    }
}