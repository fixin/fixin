<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where;

use Fixin\Resource\PrototypeInterface;
use Fixin\Model\Request\RequestInterface;

interface WhereInterface extends PrototypeInterface {

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
     * @param number|string $left
     * @param string $operator
     * @param number|string|array $right
     * @return self
     */
    public function compare($left, string $operator, $right): self;

    /**
     * Add: and exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function exists(RequestInterface $request): self;

    /**
     * Add: and in
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function in(string $identifier, array $values): self;

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
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function notIn(string $identifier, array $values): self;

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
     * @param number|string $left
     * @param string $operator
     * @param number|string|array $right
     * @return self
     */
    public function orCompare($left, string $operator, $right): self;

    /**
     * Add: or exists
     *
     * @param RequestInterface $request
     * @return self
     */
    public function orExists(RequestInterface $request): self;

    /**
     * Add: or in
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function orIn(string $identifier, array $values): self;

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
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function orNotIn(string $identifier, array $values): self;

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
}
