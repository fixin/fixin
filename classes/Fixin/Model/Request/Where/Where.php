<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where;

use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\TagInterface;
use Fixin\Model\Request\Where\Tag\WhereTag;
use Fixin\Resource\Prototype;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 *
 */
class Where extends Prototype implements WhereInterface {

    const PROTOTYPE_BETWEEN_TAG = 'Model\Request\Where\Tag\BetweenTag';
    const PROTOTYPE_COMPARE_TAG = 'Model\Request\Where\Tag\CompareTag';
    const PROTOTYPE_EXISTS_TAG = 'Model\Request\Where\Tag\ExistsTag';
    const PROTOTYPE_EXPRESSION = 'Model\Request\Expression';
    const PROTOTYPE_IN_TAG = 'Model\Request\Where\Tag\InTag';
    const PROTOTYPE_NULL_TAG = 'Model\Request\Where\Tag\NullTag';
    const PROTOTYPE_WHERE = 'Model\Request\Where\Where';
    const PROTOTYPE_WHERE_TAG = 'Model\Request\Where\Tag\WhereTag';

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * Add compare tag
     *
     * @param string $join
     * @param unknown $left
     * @param string $operator
     * @param unknown $right
     * @param string $leftType
     * @param string $rightType
     * @return self
     */
    protected function addCompare(string $join, $left, string $operator, $right, string $leftType, string $rightType): self {
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_COMPARE_TAG, [
            CompareTag::OPTION_JOIN => $join,
            CompareTag::OPTION_LEFT => $this->compareSidePrepare($left, $leftType),
            CompareTag::OPTION_OPERATOR => $operator,
            CompareTag::OPTION_RIGHT => $this->compareSidePrepare($right, $rightType)
        ]);

        return $this;
    }

    /**
     * Add exists
     *
     * @param string $join
     * @param bool $negated
     * @param RequestInterface $request
     * @return self
     */
    protected function addExists(string $join, bool $negated, RequestInterface $request) {
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_EXISTS_TAG, [
            ExistsTag::OPTION_JOIN => $join,
            ExistsTag::OPTION_NEGATED => $negated,
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }
    /**
     * Add items
     *
     * @param Where $where
     * @param array $array
     */
    protected function addItems(Where $where, array $array) {
        foreach ($array as $key => $value) {
            // Simple where (no key)
            if (is_numeric($key)) {
                $where->sub($value);

                continue;
            }

            // Key - array value
            if (is_array($value)) {
                $where->in($key, $value);

                continue;
            }

            // Key - any value
            $where->compare($key, CompareTag::OPERATOR_EQUAL, $value);
        }
    }

    /**
     * Add nested where
     *
     * @param string $join
     * @param bool $negated
     * @param callable $callback
     * @return self
     */
    protected function addNested(string $join, bool $negated, callable $callback) {
        $where = $this->container->clonePrototype(static::PROTOTYPE_WHERE);
        $callback($where);

        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_WHERE_TAG, [
            WhereTag::OPTION_JOIN => $join,
            WhereTag::OPTION_NEGATED => $negated,
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * Add null
     *
     * @param string $join
     * @param bool $negated
     * @param string $identifier
     * @return self
     */
    protected function addNull(string $join, bool $negated, string $identifier) {
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_NULL_TAG, [
            NullTag::OPTION_JOIN => $join,
            NullTag::OPTION_NEGATED => $negated,
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::between($identifier, $min, $max)
     */
    public function between(string $identifier, $min, $max): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_BETWEEN_TAG, [
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::compare($left, $operator, $right, $leftType, $rightType)
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface {
        return $this->addCompare(TagInterface::JOIN_AND, $left, $operator, $right, $leftType, $rightType);
    }

    /**
     * Compare side prepare
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function compareSidePrepare($value, string $type) {
        if (!$value instanceof ExpressionInterface && !$value instanceof RequestInterface && $type === static::TYPE_IDENTIFIER) {
            return $this->container->clonePrototype(static::PROTOTYPE_EXPRESSION, [
                ExpressionInterface::OPTION_EXPRESSION => $value
            ]);
        }

        return $value;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_IN_TAG, [
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_BETWEEN_TAG, [
            BetweenTag::OPTION_NEGATED => true,
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_IN_TAG, [
            InTag::OPTION_NEGATED => true,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_BETWEEN_TAG, [
            BetweenTag::OPTION_JOIN => TagInterface::JOIN_OR,
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orCompare($left, $operator, $right, $leftType, $rightType)
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface {
        return $this->addCompare(TagInterface::JOIN_OR, $left, $operator, $right, $leftType, $rightType);
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_IN_TAG, [
            InTag::OPTION_JOIN => TagInterface::JOIN_OR,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_BETWEEN_TAG, [
            BetweenTag::OPTION_JOIN => TagInterface::JOIN_OR,
            BetweenTag::OPTION_NEGATED => true,
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_IN_TAG, [
            InTag::OPTION_JOIN => TagInterface::JOIN_OR,
            InTag::OPTION_NEGATED => true,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
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
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_WHERE_TAG, [
            WhereTag::OPTION_JOIN => TagInterface::JOIN_OR,
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::sub($where)
     */
    public function sub(WhereInterface $where): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::PROTOTYPE_WHERE_TAG, [
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }
}