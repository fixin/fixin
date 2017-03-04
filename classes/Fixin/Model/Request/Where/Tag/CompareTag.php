<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class CompareTag extends Tag {

    protected const
        THIS_REQUIRES = [
        self::OPTION_LEFT => self::TYPE_ANY,
        self::OPTION_OPERATOR => self::TYPE_STRING,
        self::OPTION_RIGHT => self::TYPE_ANY,
    ];

    public const
        OPERATOR_EQUAL = '=',
        OPERATOR_GREATER_THAN = '>',
        OPERATOR_GREATER_THAN_OR_EQUAL = '>=',
        OPERATOR_LOWER_THAN = '<',
        OPERATOR_LOWER_THAN_OR_EQUAL = '<=',
        OPERATOR_NOT_EQUAL = '!=',
        OPTION_LEFT = 'left',
        OPTION_OPERATOR = 'operator',
        OPTION_RIGHT = 'right';

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

    /**
     * Set left side
     *
     * @param number|string $left
     */
    protected function setLeft($left): void
    {
        $this->left = $left;
    }

    protected function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * Set right side
     *
     * @param number|string|array $right
     */
    protected function setRight($right): void
    {
        $this->right = $right;
    }
}
