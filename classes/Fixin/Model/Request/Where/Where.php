<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where;

use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\RequestTag;
use Fixin\Model\Request\Where\Tag\TagInterface;
use Fixin\Model\Request\Where\Tag\WhereTag;
use Fixin\Resource\Prototype;

class Where extends Prototype implements WhereInterface {

    const BETWEEN_TAG_PROTOTYPE = 'Model\Request\Where\Tag\BetweenTag';
    const COMPARE_TAG_PROTOTYPE = 'Model\Request\Where\Tag\CompareTag';
    const EXISTS_TAG_PROTOTYPE = 'Model\Request\Where\Tag\ExistsTag';
    const IN_TAG_PROTOTYPE = 'Model\Request\Where\Tag\InTag';
    const NULL_TAG_PROTOTYPE = 'Model\Request\Where\Tag\NullTag';
    const REQUEST_TAG_PROTOTYPE = 'Model\Request\Where\Tag\RequestTag';
    const WHERE_TAG_PROTOTYPE = 'Model\Request\Where\Tag\WhereTag';

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::between($identifier, $min, $max)
     */
    public function between(string $identifier, $min, $max): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::BETWEEN_TAG_PROTOTYPE, [
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::compare($left, $operator, $right)
     */
    public function compare($left, string $operator, $right): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::COMPARE_TAG_PROTOTYPE, [
            CompareTag::OPTION_LEFT => $left,
            CompareTag::OPTION_OPERATOR => $operator,
            CompareTag::OPTION_RIGHT => $right
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::exists($request)
     */
    public function exists(RequestInterface $request): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::EXISTS_TAG_PROTOTYPE, [
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::in($identifier, $values)
     */
    public function in(string $identifier, array $values): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /* TODO: items
    protected function addItems(Where $where, array $array) {
        foreach ($array as $key => $value) {
            // Simple where (no key)
            if (is_numeric($key)) {
                $request->request($where);

                continue;
            }

            // Key - array value
            if (is_array($value)) {
                $where->in($key, $value);

                continue;
            }

            // Key - any value
            $where->compare($key, static::OPERATOR_EQUALS, $value);
        }
    }

    public function items(array $array): WhereInterface {
        return $this->nested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    public function orItems(array $array): WhereInterface {
        return $this->orNested(function(Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }*/

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::nested($callback)
     */
    public function nested(callable $callback): WhereInterface {
        $where = new static();
        $callback($where);

        $this->tags[] = $this->container->clonePrototype(static::WHERE_TAG_PROTOTYPE, [
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notBetween($identifier, $min, $max)
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::BETWEEN_TAG_PROTOTYPE, [
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
        $this->tags[] = $this->container->clonePrototype(static::EXISTS_TAG_PROTOTYPE, [
            ExistsTag::OPTION_NEGATED => true,
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notIn($identifier, $values)
     */
    public function notIn(string $identifier, array $values): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
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
        $where = new static();
        $callback($where);

        $this->tags[] = $this->container->clonePrototype(static::WHERE_TAG_PROTOTYPE, [
            WhereTag::OPTION_NEGATED => true,
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notNull($identifier)
     */
    public function notNull(string $identifier): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_NEGATED => true,
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::null($identifier)
     */
    public function null(string $identifier): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orBetween($identifier, $min, $max)
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::BETWEEN_TAG_PROTOTYPE, [
            BetweenTag::OPTION_JOIN => TagInterface::JOIN_OR,
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orCompare($left, $operator, $right)
     */
    public function orCompare($left, string $operator, $right): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::COMPARE_TAG_PROTOTYPE, [
            CompareTag::OPTION_JOIN => TagInterface::JOIN_OR,
            CompareTag::OPTION_LEFT => $left,
            CompareTag::OPTION_OPERATOR => $operator,
            CompareTag::OPTION_RIGHT => $right
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orExists($request)
     */
    public function orExists(RequestInterface $request): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::EXISTS_TAG_PROTOTYPE, [
            ExistsTag::OPTION_JOIN => TagInterface::JOIN_OR,
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orIn($identifier, $values)
     */
    public function orIn(string $identifier, array $values): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
            InTag::OPTION_JOIN => TagInterface::JOIN_OR,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNested($callback)
     */
    public function orNested(callable $callback): WhereInterface {
        $where = new static();
        $callback($where);

        $this->tags[] = $this->container->clonePrototype(static::WHERE_TAG_PROTOTYPE, [
            WhereTag::OPTION_JOIN => TagInterface::JOIN_OR,
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotBetween($identifier, $min, $max)
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::BETWEEN_TAG_PROTOTYPE, [
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
        $this->tags[] = $this->container->clonePrototype(static::EXISTS_TAG_PROTOTYPE, [
            ExistsTag::OPTION_JOIN => TagInterface::JOIN_OR,
            ExistsTag::OPTION_NEGATED => true,
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotIn($identifier, $values)
     */
    public function orNotIn(string $identifier, array $values): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
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
        $where = new static();
        $callback($where);

        $this->tags[] = $this->container->clonePrototype(static::WHERE_TAG_PROTOTYPE, [
            WhereTag::OPTION_JOIN => TagInterface::JOIN_OR,
            WhereTag::OPTION_NEGATED => true,
            WhereTag::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotNull($identifier)
     */
    public function orNotNull(string $identifier): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_JOIN => TagInterface::JOIN_OR,
            NullTag::OPTION_NEGATED => true,
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNull($identifier)
     */
    public function orNull(string $identifier): WhereInterface {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_JOIN => TagInterface::JOIN_OR,
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }
}