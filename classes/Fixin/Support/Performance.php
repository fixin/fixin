<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Performance extends DoNotCreate {

    const MEASURE_FORMAT = "\n"
        . "    Elapsed time:       %12s ms\n"
        . "    Memory change:      %12s bytes\n"
        . "    Memory peak:        %12s bytes\n"
        . "    Memory system peak: %12s bytes\n\n";

    /**
     * @var number
     */
    protected static $lastTime = null;

    /**
     * @var int
     */
    protected static $lastMemoryUsage;

    /**
     * Measurement for performance tests
     *
     * Elapsed time, memory change, memory peak, memory system peak
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
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
     * Measurement for code
     *
     * @param \Closure $function
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function measureCode(\Closure $function) {
        echo "\n" . CodeInspector::functionSource($function);

        static::$lastTime = microtime(true);
        static::$lastMemoryUsage = memory_get_usage();

        $function();

        static::measure();
    }

    /**
     * Display info
     *
     * @param number $time
     * @param int $memoryUsage
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
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