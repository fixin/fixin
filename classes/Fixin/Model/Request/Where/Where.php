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
     * @return $this
     */
    public function between(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_AND, false, $identifier, $min, $max);

        return $this;
    }

    /**
     * @return $this
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface
    {
        $this->addCompare(TagInterface::JOIN_AND, false, $left, $operator, $right, $leftType, $rightType);

        return $this;
    }

    /**
     * @return $this
     */
    public function exists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_AND, false, $request);

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return $this
     */
    public function id(EntityIdInterface $entityId): WhereInterface
    {
        return $this->items($entityId->getArrayCopy());
    }

    /**
     * @return $this
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
     * @return $this
     */
    public function in($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_AND, false, $identifier, $values);

        return $this;
    }

    /**
     * @return $this
     */
    public function items(array $array): WhereInterface
    {
        return $this->nested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * @return $this
     */
    public function nested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_AND, false, $callback);

        return $this;
    }

    /**
     * @return $this
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_AND, true, $identifier, $min, $max);

        return $this;
    }

    /**
     * @return $this
     */
    public function notExists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_AND, true, $request);

        return $this;
    }

    /**
     * @return $this
     */
    public function notIn($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_AND, true, $identifier, $values);

        return $this;
    }

    /**
     * @return $this
     */
    public function notNested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_AND, true, $callback);

        return $this;
    }

    /**
     * @return $this
     */
    public function notNull(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_AND, true, $identifier);

        return $this;
    }

    /**
     * @return $this
     */
    public function null(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_AND, false, $identifier);

        return $this;
    }

    /**
     * @return $this
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_OR, false, $identifier, $min, $max);

        return $this;
    }

    /**
     * @return $this
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface
    {
        $this->addCompare(TagInterface::JOIN_OR, false, $left, $operator, $right, $leftType, $rightType);

        return $this;
    }

    /**
     * @return $this
     */
    public function orExists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_OR, false, $request);

        return $this;
    }

    /**
     * @return $this
     */
    public function orId(EntityIdInterface $entityId): WhereInterface
    {
        return $this->orItems($entityId->getArrayCopy());
    }

    /**
     * @return $this
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
     * @return $this
     */
    public function orIn($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_OR, false, $identifier, $values);

        return $this;
    }

    /**
     * @return $this
     */
    public function orItems(array $array): WhereInterface
    {
        return $this->orNested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * @return $this
     */
    public function orNested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_OR, false, $callback);

        return $this;
    }

    /**
     * @return $this
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_OR, true, $identifier, $min, $max);

        return $this;
    }

    /**
     * @return $this
     */
    public function orNotExists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_OR, true, $request);

        return $this;
    }

    /**
     * @return $this
     */
    public function orNotIn($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_OR, true, $identifier, $values);

        return $this;
    }

    /**
     * @return $this
     */
    public function orNotNested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_OR, true, $callback);

        return $this;
    }

    /**
     * @return $this
     */
    public function orNotNull(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_OR, true, $identifier);

        return $this;
    }

    /**
     * @return $this
     */
    public function orNull(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_OR, false, $identifier);

        return $this;
    }

    /**
     * @return $this
     */
    public function orSub(WhereInterface $where): WhereInterface
    {
        $this->addSub(WhereTag::JOIN_OR, false, $where);

        return $this;
    }

    /**
     * @return $this
     */
    public function sub(WhereInterface $where): WhereInterface
    {
        $this->addSub(WhereTag::JOIN_AND, false, $where);

        return $this;
    }
}
