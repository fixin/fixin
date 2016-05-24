<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Performance extends DoNotCreate {

    const MEASURE_FORMAT = "\nElapsed time:       %12s ms\n"
        . "Memory change:      %12s bytes\n"
        . "Memory peak:        %12s bytes\n"
        . "Memory system peak: %12s bytes\n\n";

    static $lastTime = null;
    static $lastMemoryUsage;

    /**
     * Measurement for performance tests
     *
     * Elapsed time, memory change, memory peak, memory system peak
     */
    public static function measure() {
        $memoryUsage = memory_get_usage();

        // Start
        if (is_null(static::$lastTime)) {
            echo Ground::debugText("[Performance Measurement start]\n");

            static::$lastTime = microtime(true);
            static::$lastMemoryUsage = $memoryUsage;

            return;
        }

        // Info
        static::measureInfo(microtime(true), $memoryUsage);

        // Store current
        static::$lastTime = microtime(true);
        static::$lastMemoryUsage = $memoryUsage;
    }

    /**
     * Display info
     *
     * @param int $time
     * @param int $memoryUsage
     */
    protected static function measureInfo($time, $memoryUsage) {
        $data = [
            $memoryUsage - static::$lastMemoryUsage,
            memory_get_peak_usage(),
            memory_get_peak_usage(true)
        ];
        echo Ground::debugText(vsprintf(static::MEASURE_FORMAT, array_merge([number_format(($time - static::$lastTime) * 1000, 4)], array_map('number_format', $data))));
    }
}