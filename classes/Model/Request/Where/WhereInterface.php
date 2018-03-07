<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where;

use DateTimeImmutable;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\TagInterface;
use Fixin\Resource\PrototypeInterface;

interface WhereInterface extends PrototypeInterface
{
    public const
        TYPE_IDENTIFIER = 'identifier',
        TYPE_VALUE = 'value';

    /**
     * Add: and between
     *
     * @param string $identifier
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function between(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: and compare
     *
     * @param string|number|bool $left
     * @param string $operator
     * @param string|number|bool|array|DateTimeImmutable $right
     * @param string $leftType
     * @param string $rightType
     * @return $this
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface;

    /**
     * Add: and exists
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function exists(RequestInterface $request): WhereInterface;

    /**
     * Get tags
     *
     * @return TagInterface[]
     */
    public function getTags(): array;

    /**
     * Add: and id
     *
     * @param EntityIdInterface $entityId
     * @return $this
     */
    public function id(EntityIdInterface $entityId): WhereInterface;

    /**
     * Add: and ids
     *
     * @param EntityIdInterface[] $entityIds
     * @return $this
     */
    public function ids(array $entityIds): WhereInterface;

    /**
     * Add: and in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return $this
     */
    public function in($identifier, $values): WhereInterface;

    /**
     * Add: and items from array
     *
     * @param array $array
     * @return $this
     */
    public function items(array $array): WhereInterface;

    /**
     * Add: and nested where
     *
     * @param callable $callback
     * @return $this
     */
    public function nested(callable $callback): WhereInterface;

    /**
     * Add: and not between
     *
     * @param string $identifier
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: and not exists
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function notExists(RequestInterface $request): WhereInterface;

    /**
     * Add: and not in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return $this
     */
    public function notIn($identifier, $values): WhereInterface;

    /**
     * Add: and not nested where
     *
     * @param callable $callback
     * @return $this
     */
    public function notNested(callable $callback): WhereInterface;

    /**
     * Add: and not null
     *
     * @param string $identifier
     * @return $this
     */
    public function notNull(string $identifier): WhereInterface;

    /**
     * Add: and null
     *
     * @param string $identifier
     * @return $this
     */
    public function null(string $identifier): WhereInterface;

    /**
     * Add: or between
     *
     * @param string $identifier
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: or compare
     *
     * @param string|number|bool $left
     * @param string $operator
     * @param string|number|bool|array|DateTimeImmutable $right
     * @param string $leftType
     * @param string $rightType
     * @return $this
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface;

    /**
     * Add: or exists
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function orExists(RequestInterface $request): WhereInterface;

    /**
     * Add: or id
     *
     * @param EntityIdInterface $entityId
     * @return $this
     */
    public function orId(EntityIdInterface $entityId): WhereInterface;

    /**
     * Add: or ids
     *
     * @param EntityIdInterface[] $entityIds
     * @return $this
     */
    public function orIds(array $entityIds): WhereInterface;

    /**
     * Add: or in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return $this
     */
    public function orIn($identifier, $values): WhereInterface;

    /**
     * Add: or items from array
     *
     * @param array $array
     * @return $this
     */
    public function orItems(array $array): WhereInterface;

    /**
     * Add: or nested where
     *
     * @param callable $callback
     * @return $this
     */
    public function orNested(callable $callback): WhereInterface;

    /**
     * Add: or not between
     *
     * @param string $identifier
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: or not exists
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function orNotExists(RequestInterface $request): WhereInterface;

    /**
     * Add: or not in
     *
     * @param string|array $identifier
     * @param array|RequestInterface $values
     * @return $this
     */
    public function orNotIn($identifier, $values): WhereInterface;

    /**
     * Add: or not nested where
     *
     * @param callable $callback
     * @return $this
     */
    public function orNotNested(callable $callback): WhereInterface;

    /**
     * Add: or not null
     *
     * @param string $identifier
     * @return $this
     */
    public function orNotNull(string $identifier): WhereInterface;

    /**
     * Add: or null
     *
     * @param string $identifier
     * @return $this
     */
    public function orNull(string $identifier): WhereInterface;

    /**
     * Add: or sub where
     *
     * @param WhereInterface $where
     * @return $this
     */
    public function orSub(WhereInterface $where): WhereInterface;

    /**
     * Add: and sub where
     *
     * @param WhereInterface $where
     * @return $this
     */
    public function sub(WhereInterface $where): WhereInterface;
}
