<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where;

use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\TagInterface;
use Fixin\Model\Request\Where\Tag\WhereTag;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Where extends WhereBase
{
    /**
     * @return static
     */
    public function between(string $identifier, $min, $max): WhereInterface
    {
        return $this->addBetween(BetweenTag::JOIN_AND, false, $identifier, $min, $max);
    }

    /**
     * @return static
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface
    {
        return $this->addCompare(TagInterface::JOIN_AND, false, $left, $operator, $right, $leftType, $rightType);
    }

    /**
     * @return static
     */
    public function exists(RequestInterface $request): WhereInterface
    {
        return $this->addExists(ExistsTag::JOIN_AND, false, $request);
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return static
     */
    public function id(EntityIdInterface $entityId): WhereInterface
    {
        return $this->items($entityId->getArrayCopy());
    }

    /**
     * @return static
     */
    public function ids(array $entityIds): WhereInterface
    {
        $list = [];
        foreach ($entityIds as $entityId) {
            $list[] = $entityId->getArrayCopy();
        }

        return $this->in(array_keys(reset($list)), $list);
    }

    /**
     * @return static
     */
    public function in($identifier, $values): WhereInterface
    {
        return $this->addIn(InTag::JOIN_AND, false, $identifier, $values);
    }

    /**
     * @return static
     */
    public function items(array $array): WhereInterface
    {
        return $this->nested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * @return static
     */
    public function nested(callable $callback): WhereInterface
    {
        return $this->addNested(WhereTag::JOIN_AND, false, $callback);
    }

    /**
     * @return static
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface
    {
        return $this->addBetween(BetweenTag::JOIN_AND, true, $identifier, $min, $max);
    }

    /**
     * @return static
     */
    public function notExists(RequestInterface $request): WhereInterface
    {
        return $this->addExists(ExistsTag::JOIN_AND, true, $request);
    }

    /**
     * @return static
     */
    public function notIn($identifier, $values): WhereInterface
    {
        return $this->addIn(InTag::JOIN_AND, true, $identifier, $values);
    }

    /**
     * @return static
     */
    public function notNested(callable $callback): WhereInterface
    {
        return $this->addNested(WhereTag::JOIN_AND, true, $callback);
    }

    /**
     * @return static
     */
    public function notNull(string $identifier): WhereInterface
    {
        return $this->addNull(NullTag::JOIN_AND, true, $identifier);
    }

    /**
     * @return static
     */
    public function null(string $identifier): WhereInterface
    {
        return $this->addNull(NullTag::JOIN_AND, false, $identifier);
    }

    /**
     * @return static
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface
    {
        return $this->addBetween(BetweenTag::JOIN_OR, false, $identifier, $min, $max);
    }

    /**
     * @return static
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface
    {
        return $this->addCompare(TagInterface::JOIN_OR, false, $left, $operator, $right, $leftType, $rightType);
    }

    /**
     * @return static
     */
    public function orExists(RequestInterface $request): WhereInterface
    {
        return $this->addExists(ExistsTag::JOIN_OR, false, $request);
    }

    /**
     * @return static
     */
    public function orId(EntityIdInterface $entityId): WhereInterface
    {
        return $this->orItems($entityId->getArrayCopy());
    }

    /**
     * @return static
     */
    public function orIds(array $entityIds): WhereInterface
    {
        $list = [];
        foreach ($entityIds as $entityId) {
            $list[] = $entityId->getArrayCopy();
        }

        return $this->orIn(array_keys(reset($list)), $list);
    }

    /**
     * @return static
     */
    public function orIn($identifier, $values): WhereInterface
    {
        return $this->addIn(InTag::JOIN_OR, false, $identifier, $values);
    }

    /**
     * @return static
     */
    public function orItems(array $array): WhereInterface
    {
        return $this->orNested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * @return static
     */
    public function orNested(callable $callback): WhereInterface
    {
        return $this->addNested(WhereTag::JOIN_OR, false, $callback);
    }

    /**
     * @return static
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface
    {
        return $this->addBetween(BetweenTag::JOIN_OR, true, $identifier, $min, $max);
    }

    /**
     * @return static
     */
    public function orNotExists(RequestInterface $request): WhereInterface
    {
        return $this->addExists(ExistsTag::JOIN_OR, true, $request);
    }

    /**
     * @return static
     */
    public function orNotIn($identifier, $values): WhereInterface
    {
        return $this->addIn(InTag::JOIN_OR, true, $identifier, $values);
    }

    /**
     * @return static
     */
    public function orNotNested(callable $callback): WhereInterface
    {
        return $this->addNested(WhereTag::JOIN_OR, true, $callback);
    }

    /**
     * @return static
     */
    public function orNotNull(string $identifier): WhereInterface
    {
        return $this->addNull(NullTag::JOIN_OR, true, $identifier);
    }

    /**
     * @return static
     */
    public function orNull(string $identifier): WhereInterface
    {
        return $this->addNull(NullTag::JOIN_OR, false, $identifier);
    }

    /**
     * @return static
     */
    public function orSub(WhereInterface $where): WhereInterface
    {
        return $this->addSub(WhereTag::JOIN_OR, false, $where);
    }

    /**
     * @return static
     */
    public function sub(WhereInterface $where): WhereInterface
    {
        return $this->addSub(WhereTag::JOIN_AND, false, $where);
    }
}
