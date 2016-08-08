<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

use Fixin\Model\Repository\RepositoryRequestInterface;

class WhereCompare extends Where {

    const OPTION_LEFT = 'left';
    const OPTION_OPERATOR = 'operator';
    const OPTION_RIGHT = 'right';
    const THIS_REQUIRES = [
        self::OPTION_LEFT => self::TYPE_ANY,
        self::OPTION_OPERATOR => self::TYPE_STRING,
        self::OPTION_RIGHT => self::TYPE_ANY,
    ];

    /**
     * @var number|string|RepositoryRequestInterface
     */
    protected $left;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var number|string|RepositoryRequestInterface|array
     */
    protected $right;

    /**
     * Get left side
     *
     * @return number|string|RepositoryRequestInterface
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
     * @return number|string|RepositoryRequestInterface
     */
    public function getRight() {
        return $this->right;
    }

    /**
     * Set left side
     *
     * @param number|string|RepositoryRequestInterface $left
     */
    protected function setLeft($left) {
        $this->left = $left instanceof \Closure ? $this->closureToRequest($left) : $left;
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
     * @param number|string|RepositoryRequestInterface|array $right
     */
    protected function setRight($right) {
        $this->right = $right instanceof \Closure ? $this->closureToRequest($right) : $right;
    }
}
