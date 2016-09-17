<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where;

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
class Where extends WhereBase {

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::between($identifier, $min, $max)
     */
    public function between(string $identifier, $min, $max): WhereInterface {
        return $this->addBetween(BetweenTag::JOIN_AND, false, $identifier, $min, $max);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::compare($left, $operator, $right, $leftType, $rightType)
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface {
        return $this->addCompare(TagInterface::JOIN_AND, false, $left, $operator, $right, $leftType, $rightType);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::exists($request)
     */
    public function exists(RequestInterface $request): WhereInterface {
        return $this->addExists(ExistsTag::JOIN_AND, false, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::getTags()
     */
    public function getTags(): array {
        return $this->tags;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::in($identifier, $values)
     */
    public function in($identifier, $values): WhereInterface {
        return $this->addIn(InTag::JOIN_AND, false, $identifier, $values);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::items($array)
     */
    public function items(array $array): WhereInterface {
        return $this->nested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::nested($callback)
     */
    public function nested(callable $callback): WhereInterface {
        return $this->addNested(WhereTag::JOIN_AND, false, $callback);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notBetween($identifier, $min, $max)
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface {
        return $this->addBetween(BetweenTag::JOIN_AND, true, $identifier, $min, $max);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notExists($request)
     */
    public function notExists(RequestInterface $request): WhereInterface {
        return $this->addExists(ExistsTag::JOIN_AND, true, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notIn($identifier, $values)
     */
    public function notIn($identifier, $values): WhereInterface {
        return $this->addIn(InTag::JOIN_AND, true, $identifier, $values);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notNested($callback)
     */
    public function notNested(callable $callback): WhereInterface {
        return $this->addNested(WhereTag::JOIN_AND, true, $callback);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notNull($identifier)
     */
    public function notNull(string $identifier): WhereInterface {
        return $this->addNull(NullTag::JOIN_AND, true, $identifier);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::null($identifier)
     */
    public function null(string $identifier): WhereInterface {
        return $this->addNull(NullTag::JOIN_AND, false, $identifier);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orBetween($identifier, $min, $max)
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface {
        return $this->addBetween(BetweenTag::JOIN_OR, false, $identifier, $min, $max);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orCompare($left, $operator, $right, $leftType, $rightType)
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface {
        return $this->addCompare(TagInterface::JOIN_OR, false, $left, $operator, $right, $leftType, $rightType);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orExists($request)
     */
    public function orExists(RequestInterface $request): WhereInterface {
        return $this->addExists(ExistsTag::JOIN_OR, false, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orIn($identifier, $values)
     */
    public function orIn($identifier, $values): WhereInterface {
        return $this->addIn(InTag::JOIN_OR, false, $identifier, $values);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orItems($array)
     */
    public function orItems(array $array): WhereInterface {
        return $this->orNested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNested($callback)
     */
    public function orNested(callable $callback): WhereInterface {
        return $this->addNested(WhereTag::JOIN_OR, false, $callback);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotBetween($identifier, $min, $max)
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface {
        return $this->addBetween(BetweenTag::JOIN_OR, true, $identifier, $min, $max);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotExists($request)
     */
    public function orNotExists(RequestInterface $request): WhereInterface {
        return $this->addExists(ExistsTag::JOIN_OR, true, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotIn($identifier, $values)
     */
    public function orNotIn($identifier, $values): WhereInterface {
        return $this->addIn(InTag::JOIN_OR, true, $identifier, $values);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotNested($callback)
     */
    public function orNotNested(callable $callback): WhereInterface {
        return $this->addNested(WhereTag::JOIN_OR, true, $callback);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotNull($identifier)
     */
    public function orNotNull(string $identifier): WhereInterface {
        return $this->addNull(NullTag::JOIN_OR, true, $identifier);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNull($identifier)
     */
    public function orNull(string $identifier): WhereInterface {
        return $this->addNull(NullTag::JOIN_OR, false, $identifier);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orSub($where)
     */
    public function orSub(WhereInterface $where): WhereInterface {
        return $this->addSub(WhereTag::JOIN_OR, false, $where);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::sub($where)
     */
    public function sub(WhereInterface $where): WhereInterface {
        return $this->addSub(WhereTag::JOIN_AND, false, $where);
    }
}