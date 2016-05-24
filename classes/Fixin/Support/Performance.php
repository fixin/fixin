<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Performance extends DoNotCreate {

    const MEASURE_FORMAT = "\nElapsed time:       %10s ms\n"
        . "Memory change:      %10s\n"
        . "Memory peak:        %10s\n"
        . "Memory system peak: %10s\n\n";

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
            $lastMemoryUsage = $memoryUsage;

            return;
        }

        // Info
        echo Ground::debugText(sprintf(static::MEASURE_FORMAT,
            number_format((microtime(true) - $lastTime) * 1000, 4),
            number_format($memoryUsage - $lastMemoryUsage),
            number_format(memory_get_peak_usage()),
            number_format(memory_get_peak_usage(true))));

        // Store current
        $lastTime = microtime(true);
        $lastMemoryUsage = $memoryUsage;
    }
}