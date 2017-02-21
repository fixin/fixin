<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Performance extends DoNotCreate
{
    protected const
        MEASURE_FORMAT = "\n"
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
     */
    public static function measure(): void
    {
        $time = microtime(true);
        $memoryUsage = memory_get_usage();

        // Start
        if (is_null(static::$lastTime)) {
            echo Ground::debugText("[Performance Measurement start]\n");

            static::$lastMemoryUsage = $memoryUsage;
            static::$lastTime = microtime(true);

            return;
        }

        // Info
        static::measureInfo($time, $memoryUsage);

        // Store current
        static::$lastMemoryUsage = $memoryUsage;
        static::$lastTime = microtime(true);
    }

    /**
     * Measurement for code
     */
    public static function measureCode(\Closure $function): void
    {
        echo "\n" . CodeInspector::functionSource($function);

        static::$lastMemoryUsage = memory_get_usage();
        static::$lastTime = microtime(true);

        $function();

        static::measure();
    }

    /**
     * Display info
     */
    protected static function measureInfo(float $time, int $memoryUsage): void
    {
        $data = [
            $memoryUsage - static::$lastMemoryUsage,
            memory_get_peak_usage(),
            memory_get_peak_usage(true)
        ];

        echo Ground::debugText(vsprintf(static::MEASURE_FORMAT, array_merge([number_format(($time - static::$lastTime) * 1000, 4)], array_map('number_format', $data))));
    }
}
