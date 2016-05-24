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
            $lastMemoryUsage = $memoryUsage;

            return;
        }

        // Store current
        $lastTime = microtime(true);
        $lastMemoryUsage = $memoryUsage;
    }
}