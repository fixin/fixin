<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where;

use Fixin\Resource\Prototype;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\RequestTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\NullTag;

class Where extends Prototype implements WhereInterface {

    const BETWEEN_TAG_PROTOTYPE = 'Model\Request\Where\Tag\BetweenTag';
    const COMPARE_TAG_PROTOTYPE = 'Model\Request\Where\Tag\CompareTag';
    const EXISTS_TAG_PROTOTYPE = 'Model\Request\Where\Tag\ExistsTag';
    const IN_TAG_PROTOTYPE = 'Model\Request\Where\Tag\InTag';
    const NULL_TAG_PROTOTYPE = 'Model\Request\Where\Tag\NullTag';
    const REQUEST_TAG_PROTOTYPE = 'Model\Request\Where\Tag\RequestTag';

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
     * @see \Fixin\Model\Request\Where\WhereInterface::exists($callback)
     */
    public function exists(callable $callback): self {
        $this->tags[] = $this->container->clonePrototype(static::REQUEST_TAG_PROTOTYPE, [
            RequestTag::OPTION_REQUEST => $callback
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::in($identifier, $values)
     */
    public function in(string $identifier, array $values): self {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notBetween($identifier, $min, $max)
     */
    public function notBetween(string $identifier, $min, $max): self {
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
     * @see \Fixin\Model\Request\Where\WhereInterface::notExists($callback)
     */
    public function notExists(RequestInterface $request): self {
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
    public function notIn(string $identifier, array $values): self {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
            InTag::OPTION_NEGATED => true,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::notNull($identifier)
     */
    public function notNull(string $identifier): self {
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
    public function null(string $identifier): self {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orBetween($identifier, $min, $max)
     */
    public function orBetween(string $identifier, $min, $max): self {
        $this->tags[] = $this->container->clonePrototype(static::BETWEEN_TAG_PROTOTYPE, [
            BetweenTag::OPTION_JOIN => WhereInterface::JOIN_OR,
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
    public function orCompare($left, string $operator, $right): self {
        $this->tags[] = $this->container->clonePrototype(static::COMPARE_TAG_PROTOTYPE, [
            CompareTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            CompareTag::OPTION_LEFT => $left,
            CompareTag::OPTION_OPERATOR => $operator,
            CompareTag::OPTION_RIGHT => $right
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orExists($callback)
     */
    public function orExists(RequestInterface $request): self {
        $this->tags[] = $this->container->clonePrototype(static::EXISTS_TAG_PROTOTYPE, [
            ExistsTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orIn($identifier, $values)
     */
    public function orIn(string $identifier, array $values): self {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
            InTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotBetween($identifier, $min, $max)
     */
    public function orNotBetween(string $identifier, $min, $max): self {
        $this->tags[] = $this->container->clonePrototype(static::BETWEEN_TAG_PROTOTYPE, [
            BetweenTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            BetweenTag::OPTION_NEGATED => true,
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotExists($callback)
     */
    public function orNotExists(RequestInterface $request): self {
        $this->tags[] = $this->container->clonePrototype(static::EXISTS_TAG_PROTOTYPE, [
            ExistsTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            ExistsTag::OPTION_NEGATED => true,
            ExistsTag::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotIn($identifier, $values)
     */
    public function orNotIn(string $identifier, array $values): self {
        $this->tags[] = $this->container->clonePrototype(static::IN_TAG_PROTOTYPE, [
            InTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            InTag::OPTION_NEGATED => true,
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNotNull($identifier)
     */
    public function orNotNull(string $identifier): self {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            NullTag::OPTION_NEGATED => true,
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\WhereInterface::orNull($identifier)
     */
    public function orNull(string $identifier): self {
        $this->tags[] = $this->container->clonePrototype(static::NULL_TAG_PROTOTYPE, [
            NullTag::OPTION_JOIN => WhereInterface::JOIN_OR,
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }
}