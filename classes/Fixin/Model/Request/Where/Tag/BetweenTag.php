<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

class BetweenTag extends IdentifierTag
{
    protected const
        THIS_REQUIRES = [
            self::IDENTIFIER,
            self::MAX,
            self::MIN
        ];

    public const
        MAX = 'max',
        MIN = 'min';

    /**
     * @var number|string
     */
    protected $max;

    /**
     * @var number|string
     */
    protected $min;

    /**
     * Get max value
     *
     * @return number|string
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Get min value
     *
     * @return number|string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max value
     *
     * @param number|string $max
     */
    protected function setMax($max): void
    {
        $this->max = $max;
    }

    /**
     * Set min value
     *
     * @param number|string $min
     */
    protected function setMin($min): void
    {
        $this->min = $min;
    }
}
