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

    /**
     * Measurement for performance tests
     *
     * Elapsed time, memory change, memory peak, memory system peak
     */
    public static function performanceMaesure() {
        $memoryUsage = memory_get_usage();

        // Previous values
        static $lastTime = null;
        static $lastMemoryUsage = null;

        // Start
        if (is_null($lastTime)) {
            echo static::debugText("[Performance Measurement start]\n");

            $lastTime = microtime(true);
            $lastMemoryUsage = memory_get_usage();

            return;
        }

        // Info
        $info = "\nElapsed time:        " . number_format((microtime(true) - $lastTime) * 1000, 4) . " ms\n";
        $info .= 'Memory change:       ' . number_format($memoryUsage - $lastMemoryUsage) . "\n";
        $info .= 'Memory peak:         ' . number_format(memory_get_peak_usage()) . "\n";
        $info .= 'Memory system peak:  ' . number_format(memory_get_peak_usage(true)) . "\n\n";

        echo static::debugText($info);

        // Store current
        $lastTime = microtime(true);
        $lastMemoryUsage = $memoryUsage;
    }
}