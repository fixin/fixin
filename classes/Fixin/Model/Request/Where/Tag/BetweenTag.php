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
        THIS_REQUIRES = parent::THIS_REQUIRES + [
            self::MAX,
            self::MIN
        ],
        THIS_SETS = parent::THIS_SETS + [
            self::MAX => self::SCALAR_TYPE,
            self::MIN => self::SCALAR_TYPE
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
