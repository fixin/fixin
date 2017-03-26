<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class CompareTag extends Tag {

    protected const
        THIS_REQUIRES = [
            self::LEFT,
            self::OPERATOR,
            self::RIGHT
        ],
        THIS_SETS = [
            self::LEFT => self::ANY_TYPE,
            self::OPERATOR => self::STRING_TYPE,
            self::RIGHT => self::ANY_TYPE
        ];

    public const
        GREATER_THAN = '>',
        GREATER_THAN_OR_EQUALS = '>=',
        EQUALS = '=',
        LEFT = 'left',
        LOWER_THAN = '<',
        LOWER_THAN_OR_EQUALS = '<=',
        NOT_EQUALS = '!=',
        OPERATOR = 'operator',
        RIGHT = 'right';

    /**
     * @var number|string|RequestInterface
     */
    protected $left;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var number|string|RequestInterface|array
     */
    protected $right;

    /**
     * Get left side
     *
     * @return number|string
     */
    public function getLeft()
    {
        return $this->left;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Get right side
     *
     * @return number|string|array
     */
    public function getRight()
    {
        return $this->right;
    }
}
