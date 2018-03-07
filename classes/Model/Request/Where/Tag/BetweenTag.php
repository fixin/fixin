<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use DateTimeImmutable;
use Fixin\Support\Types;

class BetweenTag extends AbstractIdentifierTag
{
    public const
        MAX = 'max',
        MIN = 'min';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::MAX => [Types::SCALAR, DateTimeImmutable::class],
            self::MIN => [Types::SCALAR, DateTimeImmutable::class]
        ];

    /**
     * @var number|string|DateTimeImmutable
     */
    protected $max;

    /**
     * @var number|string|DateTimeImmutable
     */
    protected $min;

    /**
     * Get max value
     *
     * @return number|string|DateTimeImmutable
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Get min value
     *
     * @return number|string|DateTimeImmutable
     */
    public function getMin()
    {
        return $this->min;
    }
}
