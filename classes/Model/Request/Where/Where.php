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
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\ComparisonTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\TagInterface;
use Fixin\Model\Request\Where\Tag\WhereTag;
use Fixin\Resource\Prototype;
use Fixin\Support\DebugDescriptionTrait;

class Where extends Prototype implements WhereInterface
{
    use DebugDescriptionTrait;

    protected const
        BETWEEN_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\BetweenTag',
        COMPARISON_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\ComparisonTag',
        EXISTS_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\ExistsTag',
        EXPRESSION_PROTOTYPE = '*\Model\Request\Expression',
        FALSE_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\FalseTag',
        IN_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\InTag',
        NULL_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\NullTag',
        WHERE_PROTOTYPE = '*\Model\Request\Where\Where',
        WHERE_TAG_PROTOTYPE = '*\Model\Request\Where\Tag\WhereTag';

    /**
     * @var TagInterface[]
     */
    protected $tags = [];

    /**
     * Add between tag
     *
     * @param string $join
     * @param bool $positive
     * @param $identifier
     * @param $min
     * @param $max
     */
    protected function addBetween(string $join, bool $positive, $identifier, $min, $max): void
    {
        $this->addTag(static::BETWEEN_TAG_PROTOTYPE, $join, $positive, [
            BetweenTag::IDENTIFIER => $identifier,
            BetweenTag::MIN => $min,
            BetweenTag::MAX => $max
        ]);
    }

    /**
     * Add comparison tag
     *
     * @param string $join
     * @param bool $positive
     * @param $left
     * @param string $operator
     * @param $right
     * @param string $leftType
     * @param string $rightType
     */
    protected function addComparison(string $join, bool $positive, $left, string $operator, $right, string $leftType, string $rightType): void
    {
        $this->addTag(static::COMPARISON_TAG_PROTOTYPE, $join, $positive, [
            ComparisonTag::LEFT => $this->prepareComparisonSide($left, $leftType),
            ComparisonTag::OPERATOR => $operator,
            ComparisonTag::RIGHT => $this->prepareComparisonSide($right, $rightType)
        ]);
    }

    /**
     * Add exists tag
     *
     * @param string $join
     * @param bool $positive
     * @param RequestInterface $request
     */
    protected function addExists(string $join, bool $positive, RequestInterface $request): void
    {
        $this->addTag(static::EXISTS_TAG_PROTOTYPE, $join, $positive, [
            ExistsTag::REQUEST => $request
        ]);
    }

    /**
     * Add in tag
     *
     * @param string $join
     * @param bool $positive
     * @param $identifier
     * @param $values
     */
    protected function addIn(string $join, bool $positive, $identifier, $values): void
    {
        $this->addTag(static::IN_TAG_PROTOTYPE, $join, $positive, [
            InTag::IDENTIFIER => $identifier,
            InTag::VALUES => $values
        ]);
    }

    /**
     * Add items
     *
     * @param Where $where
     * @param array $array
     */
    protected function addItems(Where $where, array $array): void
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $where->sub($value);

                continue;
            }

            if (is_array($value)) {
                $where->in($key, $value);

                continue;
            }

            $where->compare($key, ComparisonTag::EQUALS, $value);
        }
    }

    /**
     * Add nested tag by callback
     *
     * @param string $join
     * @param bool $positive
     * @param callable $callback
     */
    protected function addNested(string $join, bool $positive, callable $callback): void
    {
        $where = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class);
        $callback($where);

        $this->addTag(static::WHERE_TAG_PROTOTYPE, $join, $positive, [
            WhereTag::WHERE => $where
        ]);
    }

    /**
     * Add null tag
     *
     * @param string $join
     * @param bool $positive
     * @param string $identifier
     */
    protected function addNull(string $join, bool $positive, string $identifier): void
    {
        $this->addTag(static::NULL_TAG_PROTOTYPE, $join, $positive, [
            NullTag::IDENTIFIER => $identifier
        ]);
    }

    /**
     * Add subtag
     *
     * @param string $join
     * @param bool $positive
     * @param WhereInterface $where
     */
    protected function addSub(string $join, bool $positive, WhereInterface $where): void
    {
        $this->addTag(static::WHERE_TAG_PROTOTYPE, $join, $positive, [
            WhereTag::WHERE => $where
        ]);
    }

    /**
     * Add tag
     *
     * @param string $prototype
     * @param string $join
     * @param bool $positive
     * @param array $options
     */
    protected function addTag(string $prototype, string $join, bool $positive, array $options): void
    {
        $this->tags[] = $this->resourceManager->clone($prototype, TagInterface::class, [
                TagInterface::JOIN => $join,
                TagInterface::POSITIVE => $positive
            ] + $options);
    }

    /**
     * @inheritDoc
     */
    public function between(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_AND, true, $identifier, $min, $max);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function compare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface
    {
        $this->addComparison(TagInterface::JOIN_AND, true, $left, $operator, $right, $leftType, $rightType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function exists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_AND, true, $request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function false(): WhereInterface
    {
        $this->tags[] = $this->resourceManager->clone(static::FALSE_TAG_PROTOTYPE, TagInterface::class);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @inheritDoc
     */
    public function id(EntityIdInterface $entityId): WhereInterface
    {
        return $this->items($entityId->getArrayCopy());
    }

    /**
     * @inheritDoc
     */
    public function ids(array $entityIds): WhereInterface
    {
        if (!$entityIds) {
            return $this->false();
        }

        $list = [];
        foreach ($entityIds as $entityId) {
            $list[] = $entityId->getArrayCopy();
        }

        return $this->in(array_keys(reset($list)), $list);
    }

    /**
     * @inheritDoc
     */
    public function in($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_AND, true, $identifier, $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function items(array $array): WhereInterface
    {
        return $this->nested(function (Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * @inheritDoc
     */
    public function nested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_AND, true, $callback);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notBetween(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_AND, false, $identifier, $min, $max);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notExists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_AND, false, $request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notIn($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_AND, false, $identifier, $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notNested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_AND, false, $callback);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notNull(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_AND, false, $identifier);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function null(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_AND, true, $identifier);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orBetween(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_OR, true, $identifier, $min, $max);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orCompare($left, string $operator, $right, string $leftType = self::TYPE_IDENTIFIER, string $rightType = self::TYPE_VALUE): WhereInterface
    {
        $this->addComparison(TagInterface::JOIN_OR, true, $left, $operator, $right, $leftType, $rightType);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orExists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_OR, true, $request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orId(EntityIdInterface $entityId): WhereInterface
    {
        return $this->orItems($entityId->getArrayCopy());
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function orIn($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_OR, true, $identifier, $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orItems(array $array): WhereInterface
    {
        return $this->orNested(function (Where $where) use ($array) {
            $this->addItems($where, $array);
        });
    }

    /**
     * @inheritDoc
     */
    public function orNested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_OR, true, $callback);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orNotBetween(string $identifier, $min, $max): WhereInterface
    {
        $this->addBetween(BetweenTag::JOIN_OR, false, $identifier, $min, $max);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orNotExists(RequestInterface $request): WhereInterface
    {
        $this->addExists(ExistsTag::JOIN_OR, false, $request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orNotIn($identifier, $values): WhereInterface
    {
        $this->addIn(InTag::JOIN_OR, false, $identifier, $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orNotNested(callable $callback): WhereInterface
    {
        $this->addNested(WhereTag::JOIN_OR, false, $callback);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orNotNull(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_OR, false, $identifier);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orNull(string $identifier): WhereInterface
    {
        $this->addNull(NullTag::JOIN_OR, true, $identifier);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orSub(WhereInterface $where): WhereInterface
    {
        $this->addSub(WhereTag::JOIN_OR, true, $where);

        return $this;
    }

    /**
     * Prepare comparison side
     *
     * @param $value
     * @param string $type
     * @return string
     */
    protected function prepareComparisonSide($value, string $type)
    {
        if (!$value instanceof ExpressionInterface && !$value instanceof RequestInterface && $type === static::TYPE_IDENTIFIER) {
            return $this->resourceManager->clone(static::EXPRESSION_PROTOTYPE, ExpressionInterface::class, [
                ExpressionInterface::EXPRESSION => $value
            ]);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function sub(WhereInterface $where): WhereInterface
    {
        $this->addSub(WhereTag::JOIN_AND, true, $where);

        return $this;
    }
}
