<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Support\Types;

class BetweenTag extends AbstractIdentifierTag
{
    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::MAX => Types::SCALAR,
            self::MIN => Types::SCALAR
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
}
