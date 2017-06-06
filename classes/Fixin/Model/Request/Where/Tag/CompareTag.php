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
use Fixin\Support\Types;

class CompareTag extends AbstractTag {

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::LEFT => [Types::SCALAR, RequestInterface::class],
            self::OPERATOR => Types::STRING,
            self::RIGHT => [Types::SCALAR, RequestInterface::class, Types::ARRAY]
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
