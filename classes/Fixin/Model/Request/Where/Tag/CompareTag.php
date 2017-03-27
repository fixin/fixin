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
            self::LEFT => [self::SCALAR_TYPE, RequestInterface::class],
            self::OPERATOR => self::STRING_TYPE,
            self::RIGHT => [self::SCALAR_TYPE, RequestInterface::class, self::ARRAY_TYPE]
        ];

    public const
        LEFT = 'left',
        OPERATOR = 'operator',
        RIGHT = 'right',

        GREATER_THAN = '>',
        GREATER_THAN_OR_EQUALS = '>=',
        EQUALS = '=',
        LOWER_THAN = '<',
        LOWER_THAN_OR_EQUALS = '<=',
        NOT_EQUALS = '!=';

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
     * @return number|string|RequestInterface
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
     * @return number|string|RequestInterface|array
     */
    public function getRight()
    {
        return $this->right;
    }
}
