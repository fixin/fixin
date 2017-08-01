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
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function between(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: and compare
     *
     * @param string|number|bool $left
     * @param string|number|bool|array|DateTimeImmutable $right
     * @return $this
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface;

    /**
     * Add: and exists
     *
     * @return $this
     */
    public function exists(RequestInterface $request): WhereInterface;

    /**
     * @return TagInterface[]
     */
    public function getTags(): array;

    /**
     * Add: and id
     *
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
     * @return $this
     */
    public function items(array $array): WhereInterface;

    /**
     * Add: and nested where
     *
     * @return $this
     */
    public function nested(callable $callback): WhereInterface;

    /**
     * Add: and not between
     *
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: and not exists
     *
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
     * @return $this
     */
    public function notNested(callable $callback): WhereInterface;

    /**
     * Add: and not null
     *
     * @return $this
     */
    public function notNull(string $identifier): WhereInterface;

    /**
     * Add: and null
     *
     * @return $this
     */
    public function null(string $identifier): WhereInterface;

    /**
     * Add: or between
     *
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: or compare
     *
     * @param string|number|bool $left
     * @param string|number|bool|array|DateTimeImmutable $right
     * @return $this
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface;

    /**
     * Add: or exists
     *
     * @return $this
     */
    public function orExists(RequestInterface $request): WhereInterface;

    /**
     * Add: or id
     *
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
     * @return $this
     */
    public function orItems(array $array): WhereInterface;

    /**
     * Add: or nested where
     *
     * @return $this
     */
    public function orNested(callable $callback): WhereInterface;

    /**
     * Add: or not between
     *
     * @param number|string|DateTimeImmutable $min
     * @param number|string|DateTimeImmutable $max
     * @return $this
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface;

    /**
     * Add: or not exists
     *
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
     * @return $this
     */
    public function orNotNested(callable $callback): WhereInterface;

    /**
     * Add: or not null
     *
     * @return $this
     */
    public function orNotNull(string $identifier): WhereInterface;

    /**
     * Add: or null
     *
     * @return $this
     */
    public function orNull(string $identifier): WhereInterface;

    /**
     * Add: or sub where
     *
     * @return $this
     */
    public function orSub(WhereInterface $where): WhereInterface;

    /**
     * Add: and sub where
     *
     * @return $this
     */
    public function sub(WhereInterface $where): WhereInterface;
}
