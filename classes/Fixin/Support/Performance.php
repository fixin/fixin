<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Performance extends DoNotCreate {

    /**
     * Measurement for performance tests
     *
     * Elapsed time, memory change, memory peak, memory system peak
     */
    public static function measure() {
        $memoryUsage = memory_get_usage();

        // Previous values
        static $lastTime = null;
        static $lastMemoryUsage = null;

        // Start
        if (is_null($lastTime)) {
            echo Ground::debugText("[Performance Measurement start]\n");

            $lastTime = microtime(true);
            $lastMemoryUsage = memory_get_usage();

            return;
        }

        // Info
        $info = "\nElapsed time:        " . number_format((microtime(true) - $lastTime) * 1000, 4) . " ms\n"
            . 'Memory change:       ' . number_format($memoryUsage - $lastMemoryUsage) . "\n"
            . 'Memory peak:         ' . number_format(memory_get_peak_usage()) . "\n"
            . 'Memory system peak:  ' . number_format(memory_get_peak_usage(true)) . "\n\n";

        echo Ground::debugText($info);

        // Store current
        $lastTime = microtime(true);
        $lastMemoryUsage = $memoryUsage;
    }
}