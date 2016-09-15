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
    public function between(string $identifier, $min, $max): self;

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
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): self;

    /**
     * Add: and exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function exists(RequestInterface $request): self;

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
    public function in($identifier, $values): self;

    /**
     * Add: and items from array
     *
     * @param array $array
     * @return self
     */
    public function items(array $array): self;

    /**
     * Add: and nested where
     *
     * @param callable $callback
     * @return self
     */
    public function nested(callable $callback): self;

    /**
     * Add: and not between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function notBetween(string $identifier, $min, $max): self;

    /**
     * Add: and not exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function notExists(RequestInterface $request): self;

    /**
     * Add: and not in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function notIn($identifier, $values): self;

    /**
     * Add: and not nested where
     *
     * @param callable $callback
     * @return self
     */
    public function notNested(callable $callback): self;

    /**
     * Add: and not null
     *
     * @param string $identifier
     * @return self
     */
    public function notNull(string $identifier): self;

    /**
     * Add an and null
     *
     * @param string $identifier
     * @return self
     */
    public function null(string $identifier): self;

    /**
     * Add: or between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function orBetween(string $identifier, $min, $max): self;

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
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): self;

    /**
     * Add: or exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function orExists(RequestInterface $request): self;

    /**
     * Add: or in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function orIn($identifier, $values): self;

    /**
     * Add: or items from array
     *
     * @param array $array
     * @return self
     */
    public function orItems(array $array): self;

    /**
     * Add: or nested where
     *
     * @param callable $callback
     * @return self
     */
    public function orNested(callable $callback): self;

    /**
     * Add: or not between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function orNotBetween(string $identifier, $min, $max): self;

    /**
     * Add: or not exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function orNotExists(RequestInterface $request): self;

    /**
     * Add: or not in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return self
     */
    public function orNotIn($identifier, $values): self;

    /**
     * Add: or not null
     *
     * @param string $identifier
     * @return self
     */
    public function orNotNull(string $identifier): self;

    /**
     * Add: or not nested where
     *
     * @param callable $callback
     * @return self
     */
    public function orNotNested(callable $callback): self;

    /**
     * Add: or null
     *
     * @param string $identifier
     * @return self
     */
    public function orNull(string $identifier): self;

    /**
     * Add: or sub where
     * @param self $where
     * @return self
     */
    public function orSub(self $where): self;

    /**
     * Add: and sub where
     *
     * @param self $where
     * @return self
     */
    public function sub(self $where): self;
}
