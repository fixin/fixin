<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use DateTime;
use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\WhereTag;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Resource;

abstract class GrammarBase extends Resource implements GrammarInterface
{
    protected const
        EXPRESSION_TERMINALS = "\n\r\t '\"`()[]+-*/<>!=&|^,?@",
        DATETIME_FORMAT = 'Y-m-d H:i:s',
        IDENTIFIER_QUOTE_CLOSE = "`",
        IDENTIFIER_QUOTE_OPEN = "`",
        LIST_SEPARATOR = ', ',
        LIST_SEPARATOR_MULTI_LINE = ',' . PHP_EOL . "\t",
        MASK_ALIAS = '%s AS %s',
        MASK_ARRAY = '(%s)',
        MASK_BETWEEN = 'BETWEEN %s AND %s',
        MASK_EXISTS = 'EXISTS(%s)',
        MASK_IN = 'IN %s',
        MASK_LIMIT = 'LIMIT %s' . PHP_EOL,
        MASK_NESTED = "(%s)",
        MASK_NESTED_MULTI_LINE = '(' . PHP_EOL . "\t%s)" . PHP_EOL,
        MASK_OFFSET = 'OFFSET %s' . PHP_EOL,
        MASK_ORDER_BY = 'ORDER BY %s' . PHP_EOL,
        MASK_ORDER_BY_ITEM = '%s %s',
        METHOD_WHERE_TAG = 'whereTag',
        ORDER_ASCENDING = 'ASC',
        ORDER_DESCENDING = 'DESC',
        PLACEHOLDER = '?',
        WHERE_TAG_IS_NULL = [false => 'IS NOT NULL', true => 'IS NULL'],
        WHERE_TAG_NEGATE = [false => '', true => 'NOT '],
        WHERE_TAG_SEPARATOR = PHP_EOL . "\t %s ";

    protected function expressionArrayToString(array $expression, QueryInterface $query): string
    {
        $result = [];
        foreach ($expression as $item) {
            $result[] = $this->expressionToString($item, $query);
        }

        return sprintf(static::MASK_ARRAY, implode(static::LIST_SEPARATOR, $result));
    }

    protected function expressionToString($expression, QueryInterface $query): string
    {
        // Array
        if (is_array($expression)) {
            return $this->expressionArrayToString($expression, $query);
        }

        // Expression
        if ($expression instanceof ExpressionInterface) {
            $query->addParameters($expression->getParameters());

            return $this->quoteExpression($expression->getExpression());
        }

        // DateTime
        if ($expression instanceof DateTime) {
            $expression = $expression->format(static::DATETIME_FORMAT);
        }

        // Request
        elseif ($expression instanceof RequestInterface) {
            return sprintf(static::MASK_NESTED, $this->requestToString($expression, $query));
        }

        // ID
        elseif ($expression instanceof EntityIdInterface) {
            $expression = $expression->getArrayCopy();
            if (count($expression) > 1) {
                return $this->expressionArrayToString($expression, $query);
            }

            $expression = reset($expression);
        }

        $query->addParameter($expression);

        return static::PLACEHOLDER;
    }

    /**
     * @param number|string|array|ExpressionInterface|RequestInterface $identifier
     */
    protected function identifierToString($identifier, QueryInterface $query): string
    {
        // Expression
        if ($identifier instanceof ExpressionInterface) {
            $query->addParameters($identifier->getParameters());

            $identifier = $identifier->getExpression();
        }
        // Request
        elseif ($identifier instanceof RequestInterface) {
            return sprintf(static::MASK_NESTED, $this->requestToString($identifier, $query));
        }
        elseif (is_array($identifier)) {
            return $this->quoteArrayIdentifier($identifier);
        }

        return $this->quoteIdentifier($identifier);
    }

    /**
     * Limit and offset string
     */
    protected function limitsToString(int $offset, ?int $limit): string
    {
        $result = '';

        if ($offset) {
            $result .= sprintf(static::MASK_OFFSET, $offset);
        }

        if ($limit) {
            $result .= sprintf(static::MASK_LIMIT, $limit);
        }

        return $result;
    }

    /**
     * Name (with alias)
     */
    protected function nameToString(string $name, string $alias = null): string
    {
        if ($name !== $alias && !is_null($alias)) {
            return sprintf(static::MASK_ALIAS, $this->quoteIdentifier($name), $this->quoteIdentifier($alias));
        }

        return $this->quoteIdentifier($name);
    }

    protected function orderByToString(array $orderBy, QueryInterface $query): string
    {
        if ($orderBy) {
            $list = [];

            foreach ($orderBy as $key => $value) {
                if (is_numeric($key)) {
                    $list[] = $this->identifierToString($value, $query);

                    continue;
                }

                $list[] = sprintf(static::MASK_ORDER_BY_ITEM, $this->quoteIdentifier($key), strtoupper($value) === static::ORDER_DESCENDING ? static::ORDER_DESCENDING : static::ORDER_ASCENDING);
            }

            return sprintf(static::MASK_ORDER_BY, implode(static::LIST_SEPARATOR, $list));
        }

        return '';
    }

    protected function quoteArrayIdentifier(array $identifier): string
    {
        if (count($identifier) > 1) {
            return sprintf(static::MASK_ARRAY, implode(static::LIST_SEPARATOR, array_map([$this, 'quoteIdentifier'], $identifier)));
        }

        return $this->quoteIdentifier(reset($identifier));
    }

    /**
     * Quote expression (detects identifier-only expressions)
     */
    protected function quoteExpression(string $expression): string
    {
        $trimmed = trim($expression);

        // There is no terminal characters in the trimmed
        if (strcspn($trimmed, static::EXPRESSION_TERMINALS) === strlen($trimmed)) {
            return $this->quoteIdentifier($trimmed);
        }

        // Complex expression
        return $expression;
    }

    protected function quoteIdentifier(string $identifier): string
    {
        $trimmed = trim($identifier);

        // There is no terminal characters in the trimmed
        if (strcspn($trimmed, static::EXPRESSION_TERMINALS) === strlen($trimmed)) {
            $tags = explode(static::IDENTIFIER_SEPARATOR, $trimmed);

            foreach ($tags as &$tag) {
                if ($tag[0] !== static::IDENTIFIER_QUOTE_OPEN) {
                    $tag = static::IDENTIFIER_QUOTE_OPEN . $tag . static::IDENTIFIER_QUOTE_CLOSE;
                }
            }

            return implode(static::IDENTIFIER_SEPARATOR, $tags);
        }

        return $identifier;
    }

    /**
     * Request name string (with alias)
     */
    protected function requestNameToString(RequestInterface $request): string
    {
        return $this->nameToString($request->getRepository()->getName(), $request->getAlias());
    }

    protected function requestToString(RequestInterface $request, QueryInterface $query): string
    {
        $selectQuery = $this->select($request);

        $query->addParameters($selectQuery->getParameters());

        return $selectQuery->getText();
    }

    protected function whereTagBetween(BetweenTag $tag, QueryInterface $query): string
    {
        return $this->identifierToString($tag->getIdentifier(), $query) . ' ' . static::WHERE_TAG_NEGATE[$tag->isNegated()] . sprintf(static::MASK_BETWEEN, $this->expressionToString($tag->getMin(), $query)
            , $this->expressionToString($tag->getMax(), $query));
    }

    protected function whereTagCompare(CompareTag $tag, QueryInterface $query): string
    {
        return static::WHERE_TAG_NEGATE[$tag->isNegated()] . $this->expressionToString($tag->getLeft(), $query) . ' ' . $tag->getOperator() . ' ' . $this->expressionToString($tag->getRight(), $query);
    }

    protected function whereTagExists(ExistsTag $tag, QueryInterface $query): string
    {
        return static::WHERE_TAG_NEGATE[$tag->isNegated()] . sprintf(static::MASK_EXISTS, $this->requestToString($tag->getRequest(), $query));
    }

    protected function whereTagIn(InTag $tag, QueryInterface $query): string
    {
        $values = $tag->getValues();

        return $this->identifierToString($tag->getIdentifier(), $query) . ' ' . static::WHERE_TAG_NEGATE[$tag->isNegated()] . sprintf(static::MASK_IN, $this->expressionToString($values, $query));
    }

    protected function whereTagNull(NullTag $tag, QueryInterface $query): string
    {
        return $this->identifierToString($tag->getIdentifier(), $query) . ' ' . static::WHERE_TAG_IS_NULL[!$tag->isNegated()];
    }

    /**
     * Where nested tag
     */
    protected function whereTagWhere(WhereTag $tag, QueryInterface $query): string
    {
        return sprintf(static::MASK_NESTED_MULTI_LINE, $this->whereToString('', $tag->getWhere(), $query));
    }

    protected function whereToString(string $clause, WhereInterface $where, QueryInterface $query): string
    {
        if ($tags = $where->getTags()) {
            $result = rtrim($clause . ' ');

            foreach ($tags as $index => $tag) {
                if ($index) {
                    $result .= sprintf(static::WHERE_TAG_SEPARATOR, strtoupper($tag->getJoin()));
                }

                $class = get_class($tag);
                $shortName = substr($class, strrpos($class, '\\') + 1, -3);

                $result .= $this->{static::METHOD_WHERE_TAG . $shortName}($tag, $query);
            }

            return $result . PHP_EOL;
        }

        return '';
    }
}
