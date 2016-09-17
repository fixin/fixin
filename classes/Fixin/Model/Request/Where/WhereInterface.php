<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where;

use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\PrototypeInterface;

interface WhereInterface extends PrototypeInterface {

    const TYPE_IDENTIFIER = 'identifier';
    const TYPE_VALUE = 'value';

    /**
     * Add: and between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function between(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: and compare
     *
     * @param string|number|bool $left
     * @param string $operator
     * @param string|number|bool|array $right
     * @param string $leftType
     * @param string $rightType
     * @return self
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface;

    /**
     * Add: and exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function exists(RequestInterface $request): WhereInterface;

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags(): array;

    /**
     * Add: and in
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function in($identifier, $values): WhereInterface;

    /**
     * Add: and items from array
     *
     * @param array $array
     * @return self
     */
    public function items(array $array): WhereInterface;

    /**
     * Add: and nested where
     *
     * @param callable $callback
     * @return self
     */
    public function nested(callable $callback): WhereInterface;

    /**
     * Add: and not between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: and not exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function notExists(RequestInterface $request): WhereInterface;

    /**
     * Add: and not in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function notIn($identifier, $values): WhereInterface;

    /**
     * Add: and not nested where
     *
     * @param callable $callback
     * @return self
     */
    public function notNested(callable $callback): WhereInterface;

    /**
     * Add: and not null
     *
     * @param string $identifier
     * @return self
     */
    public function notNull(string $identifier): WhereInterface;

    /**
     * Add an and null
     *
     * @param string $identifier
     * @return self
     */
    public function null(string $identifier): WhereInterface;

    /**
     * Add: or between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: or compare
     *
     * @param string|number|bool $left
     * @param string $operator
     * @param string|number|bool|array $right
     * @param string $leftType
     * @param string $rightType
     * @return self
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface;

    /**
     * Add: or exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function orExists(RequestInterface $request): WhereInterface;

    /**
     * Add: or in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function orIn($identifier, $values): WhereInterface;

    /**
     * Add: or items from array
     *
     * @param array $array
     * @return self
     */
    public function orItems(array $array): WhereInterface;

    /**
     * Add: or nested where
     *
     * @param callable $callback
     * @return self
     */
    public function orNested(callable $callback): WhereInterface;

    /**
     * Add: or not between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: or not exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function orNotExists(RequestInterface $request): WhereInterface;

    /**
     * Add: or not in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function orNotIn($identifier, $values): WhereInterface;

    /**
     * Add: or not null
     *
     * @param string $identifier
     * @return self
     */
    public function orNotNull(string $identifier): WhereInterface;

    /**
     * Add: or not nested where
     *
     * @param callable $callback
     * @return self
     */
    public function orNotNested(callable $callback): WhereInterface;

    /**
     * Add: or null
     *
     * @param string $identifier
     * @return self
     */
    public function orNull(string $identifier): WhereInterface;

    /**
     * Add: or sub where
     * @param WhereInterface $where
     * @return self
     */
    public function orSub(WhereInterface $where): WhereInterface;

    /**
     * Add: and sub where
     *
     * @param WhereInterface $where
     * @return self
     */
    public function sub(WhereInterface $where): WhereInterface;
}
