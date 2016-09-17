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

abstract class WhereBase extends Prototype implements WhereInterface {

    const
        PROTOTYPE_BETWEEN_TAG = 'Model\Request\Where\Tag\BetweenTag',
        PROTOTYPE_COMPARE_TAG = 'Model\Request\Where\Tag\CompareTag',
        PROTOTYPE_EXISTS_TAG = 'Model\Request\Where\Tag\ExistsTag',
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression',
        PROTOTYPE_IN_TAG = 'Model\Request\Where\Tag\InTag',
        PROTOTYPE_NULL_TAG = 'Model\Request\Where\Tag\NullTag',
        PROTOTYPE_WHERE = 'Model\Request\Where\Where',
        PROTOTYPE_WHERE_TAG = 'Model\Request\Where\Tag\WhereTag';

    /**
     * @var array
     */
    protected $tags = [];

    protected function addBetween(string $join, bool $negated, $identifier, $min, $max): WhereInterface {
        return $this->addTag(static::PROTOTYPE_BETWEEN_TAG, $join, $negated, [
            BetweenTag::OPTION_IDENTIFIER => $identifier,
            BetweenTag::OPTION_MIN => $min,
            BetweenTag::OPTION_MAX => $max
        ]);
    }

    protected function addCompare(string $join, bool $negated, $left, string $operator, $right, string $leftType, string $rightType): WhereInterface {
        return $this->addTag(static::PROTOTYPE_COMPARE_TAG, $join, $negated, [
            CompareTag::OPTION_LEFT => $this->compareSidePrepare($left, $leftType),
            CompareTag::OPTION_OPERATOR => $operator,
            CompareTag::OPTION_RIGHT => $this->compareSidePrepare($right, $rightType)
        ]);
    }

    protected function addExists(string $join, bool $negated, RequestInterface $request): WhereInterface {
        return $this->addTag(static::PROTOTYPE_EXISTS_TAG, $join, $negated, [
            ExistsTag::OPTION_REQUEST => $request
        ]);
    }

    protected function addIn(string $join, bool $negated, $identifier, $values): WhereInterface {
        return $this->addTag(static::PROTOTYPE_IN_TAG, $join, $negated, [
            InTag::OPTION_IDENTIFIER => $identifier,
            InTag::OPTION_VALUES => $values
        ]);
    }

    protected function addItems(Where $where, array $array): WhereInterface {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $where->sub($value);

                continue;
            }

            if (is_array($value)) {
                $where->in($key, $value);

                continue;
            }

            $where->compare($key, CompareTag::OPERATOR_EQUAL, $value);
        }

        return $this;
    }

    protected function addNested(string $join, bool $negated, callable $callback): WhereInterface {
        $where = $this->container->clonePrototype(static::PROTOTYPE_WHERE);
        $callback($where);

        return $this->addTag(static::PROTOTYPE_WHERE_TAG, $join, $negated, [
            WhereTag::OPTION_WHERE => $where
        ]);
    }

    protected function addNull(string $join, bool $negated, string $identifier): WhereInterface {
        return $this->addTag(static::PROTOTYPE_NULL_TAG, $join, $negated, [
            NullTag::OPTION_IDENTIFIER => $identifier
        ]);
    }

    protected function addSub(string $join, bool $negated, WhereInterface $where): WhereInterface {
        return $this->addTag(static::PROTOTYPE_WHERE_TAG, $join, $negated, [
            WhereTag::OPTION_WHERE => $where
        ]);
    }

    protected function addTag(string $prototype, string $join, bool $negated, array $options): WhereInterface {
        $this->tags[] = $this->container->clonePrototype($prototype, [
            TagInterface::OPTION_JOIN => $join,
            TagInterface::OPTION_NEGATED => $negated
        ] + $options);

        return $this;
    }

    protected function compareSidePrepare($value, string $type) {
        if (!$value instanceof ExpressionInterface && !$value instanceof RequestInterface && $type === static::TYPE_IDENTIFIER) {
            return $this->container->clonePrototype(static::PROTOTYPE_EXPRESSION, [
                ExpressionInterface::OPTION_EXPRESSION => $value
            ]);
        }

        return $value;
    }
}