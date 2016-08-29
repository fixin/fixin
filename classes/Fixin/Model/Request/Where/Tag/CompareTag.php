<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class CompareTag extends Tag {

    const OPERATOR_EQUAL = '=';
    const OPERATOR_GREATER_THAN = '>';
    const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';
    const OPERATOR_LOWER_THAN = '<';
    const OPERATOR_LOWER_THAN_OR_EQUAL = '<=';
    const OPERATOR_NOT_EQUAL = '!=';
    const OPTION_LEFT = 'left';
    const OPTION_OPERATOR = 'operator';
    const OPTION_RIGHT = 'right';
    const THIS_REQUIRES = [
        self::OPTION_LEFT => self::TYPE_ANY,
        self::OPTION_OPERATOR => self::TYPE_STRING,
        self::OPTION_RIGHT => self::TYPE_ANY,
    ];

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
    public function getLeft() {
        return $this->left;
    }

    /**
     * Get operator
     *
     * @return string
     */
    public function getOperator(): string {
        return $this->operator;
    }

    /**
     * Get right side
     *
     * @return number|string|array
     */
    public function getRight() {
        return $this->right;
    }

    /**
     * Set left side
     *
     * @param number|string $left
     */
    protected function setLeft($left) {
        $this->left = $left;
    }

    /**
     * Set operator
     *
     * @param string $operator
     */
    protected function setOperator(string $operator) {
        $this->operator = $operator;
    }

    /**
     * Set right side
     *
     * @param number|string|array $right
     */
    protected function setRight($right) {
        $this->right = $right;
    }
}