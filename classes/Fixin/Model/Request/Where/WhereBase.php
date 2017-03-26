<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
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

abstract class WhereBase extends Prototype implements WhereInterface
{
    protected const
        PROTOTYPE_BETWEEN_TAG = 'Model\Request\Where\Tag\BetweenTag',
        PROTOTYPE_COMPARE_TAG = 'Model\Request\Where\Tag\CompareTag',
        PROTOTYPE_EXISTS_TAG = 'Model\Request\Where\Tag\ExistsTag',
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression',
        PROTOTYPE_IN_TAG = 'Model\Request\Where\Tag\InTag',
        PROTOTYPE_NULL_TAG = 'Model\Request\Where\Tag\NullTag',
        PROTOTYPE_WHERE = 'Model\Request\Where\Where',
        PROTOTYPE_WHERE_TAG = 'Model\Request\Where\Tag\WhereTag';

    /**
     * @var TagInterface[]
     */
    protected $tags = [];

    protected function addBetween(string $join, bool $negated, $identifier, $min, $max): void
    {
        $this->addTag(static::PROTOTYPE_BETWEEN_TAG, $join, $negated, [
            BetweenTag::IDENTIFIER => $identifier,
            BetweenTag::MIN => $min,
            BetweenTag::MAX => $max
        ]);
    }

    protected function addCompare(string $join, bool $negated, $left, string $operator, $right, string $leftType, string $rightType): void
    {
        $this->addTag(static::PROTOTYPE_COMPARE_TAG, $join, $negated, [
            CompareTag::LEFT => $this->compareSidePrepare($left, $leftType),
            CompareTag::OPERATOR => $operator,
            CompareTag::RIGHT => $this->compareSidePrepare($right, $rightType)
        ]);
    }

    protected function addExists(string $join, bool $negated, RequestInterface $request): void
    {
        $this->addTag(static::PROTOTYPE_EXISTS_TAG, $join, $negated, [
            ExistsTag::REQUEST => $request
        ]);
    }

    protected function addIn(string $join, bool $negated, $identifier, $values): void
    {
        $this->addTag(static::PROTOTYPE_IN_TAG, $join, $negated, [
            InTag::IDENTIFIER => $identifier,
            InTag::VALUES => $values
        ]);
    }

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

            $where->compare($key, CompareTag::EQUALS, $value);
        }
    }

    protected function addNested(string $join, bool $negated, callable $callback): void
    {
        $where = $this->resourceManager->clone(static::PROTOTYPE_WHERE);
        $callback($where);

        $this->addTag(static::PROTOTYPE_WHERE_TAG, $join, $negated, [
            WhereTag::WHERE => $where
        ]);
    }

    protected function addNull(string $join, bool $negated, string $identifier): void
    {
        $this->addTag(static::PROTOTYPE_NULL_TAG, $join, $negated, [
            NullTag::IDENTIFIER => $identifier
        ]);
    }

    protected function addSub(string $join, bool $negated, WhereInterface $where): void
    {
        $this->addTag(static::PROTOTYPE_WHERE_TAG, $join, $negated, [
            WhereTag::WHERE => $where
        ]);
    }

    protected function addTag(string $prototype, string $join, bool $negated, array $options): void
    {
        $this->tags[] = $this->resourceManager->clone($prototype, [
            TagInterface::JOIN => $join,
            TagInterface::NEGATED => $negated
        ] + $options);
    }

    protected function compareSidePrepare($value, string $type)
    {
        if (!$value instanceof ExpressionInterface && !$value instanceof RequestInterface && $type === static::TYPE_IDENTIFIER) {
            return $this->resourceManager->clone(static::PROTOTYPE_EXPRESSION, [
                ExpressionInterface::EXPRESSION => $value
            ]);
        }

        return $value;
    }
}
