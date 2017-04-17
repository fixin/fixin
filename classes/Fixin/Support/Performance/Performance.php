<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support\Performance;

use Fixin\Support\CodeInspector;
use Fixin\Support\DoNotCreate;

class Performance extends DoNotCreate
{
    protected const
        INVALID_REPEAT_COUNT_EXCEPTION = 'Invalid repeat count';

    /**
     * @var number
     */
    protected static $lastTime = null;

    /**
     * @var int
     */
    protected static $lastMemoryUsage;

    public static function measure(string $title = null): PerformanceResult
    {
        $time = microtime(true);
        $memoryUsage = memory_get_usage();

        // Start
        if (is_null(static::$lastTime)) {
            static::$lastMemoryUsage = $memoryUsage;
            static::$lastTime = microtime(true);

            return null;
        }

        $result = new PerformanceResult(($time - static::$lastTime) * 1000, $memoryUsage - static::$lastMemoryUsage, memory_get_peak_usage(), memory_get_peak_usage(true), $title);

        // Store current
        static::$lastMemoryUsage = $memoryUsage;
        static::$lastTime = microtime(true);

        return $result;
    }

    public static function measureCode(\Closure $function): PerformanceResult
    {
        static::$lastMemoryUsage = memory_get_usage();
        static::$lastTime = microtime(true);

        $function();

        return static::measure(CodeInspector::functionSource($function));
    }
}
