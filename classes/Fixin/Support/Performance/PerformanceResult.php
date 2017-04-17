<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support\Performance;

use Fixin\Support\Ground;

class PerformanceResult
{
    protected const
        TO_STRING_FORMAT = "%s" . PHP_EOL
            . "    Elapsed time:       %12s ms" . PHP_EOL
            . "    Memory change:      %12s bytes" . PHP_EOL
            . "    Memory peak:        %12s bytes" . PHP_EOL
            . "    Memory system peak: %12s bytes" . PHP_EOL . PHP_EOL;

    /**
     * @var int
     */
    protected $elapsedTime;

    /**
     * @var int
     */
    protected $memoryChange;

    /**
     * @var int
     */
    protected $memoryPeak;

    /**
     * @var int
     */
    protected $memorySystemPeak;

    /**
     * @var string
     */
    protected $title;

    public function __construct(int $elapsedTime, int $memoryChange, int $memoryPeak, int $memorySystemPeak, string $title = 'Result')
    {
        $this->elapsedTime = $elapsedTime;
        $this->memoryChange = $memoryChange;
        $this->memoryPeak = $memoryPeak;
        $this->memorySystemPeak = $memorySystemPeak;
        $this->title = $title;
    }

    public function __toString(): string
    {
        $data = [$this->memoryChange, $this->memoryPeak, $this->memorySystemPeak];

        return Ground::toDebugText(vsprintf(static::TO_STRING_FORMAT, array_merge([$this->title, number_format($this->elapsedTime, 4)], array_map('number_format', $data))));
    }

    public function getElapsedTime(): int
    {
        return $this->elapsedTime;
    }

    public function getMemoryChange(): int
    {
        return $this->memoryChange;
    }

    public function getMemoryPeak(): int
    {
        return $this->memoryPeak;
    }

    public function getMemorySystemPeak(): int
    {
        return $this->memorySystemPeak;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
