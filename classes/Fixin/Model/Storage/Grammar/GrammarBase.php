<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\UnionInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\WhereTag;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Resource;

abstract class GrammarBase extends Resource implements GrammarInterface {

    const
        ADD_COLUMNS = 'columns',
        ALL_COLUMNS = '*',
        ADD_FROM = 'from',
        ADD_GROUP_BY = 'groupBy',
        ADD_HAVING = 'having',
        ADD_JOIN = 'join',
        ADD_LIMIT = 'limit',
        ADD_ORDER_BY = 'orderBy',
        ADD_WHERE = 'where',
        CLAUSE_FROM = 'FROM',
        CLAUSE_GROUP_BY = 'GROUP BY',
        CLAUSE_HAVING = 'HAVING',
        CLAUSE_INTO = 'INTO',
        CLAUSE_JOIN = 'JOIN',
        CLAUSE_JOIN_ON = "\tON",
        CLAUSE_SET = 'SET',
        CLAUSE_UNION = [UnionInterface::TYPE_NORMAL => 'UNION', UnionInterface::TYPE_ALL => 'UNION ALL'],
        CLAUSE_VALUES = 'VALUES',
        CLAUSE_WHERE = 'WHERE',
        EXPRESSION_TERMINALS = "\n\r\t '\"`()[]+-*/<>!=&|^,?@",
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
        METHOD_CLAUSE = 'clause',
        METHOD_WHERE_TAG = 'whereTag',
        ORDER_ASCENDING = 'ASC',
        ORDER_DESCENDING = 'DESC',
        PLACEHOLDER = '?',
        PROTOTYPE_QUERY = 'Base\Query\Query',
        WHERE_TAG_IS_NULL = [false => 'IS NOT NULL', true => 'IS NULL'],
        WHERE_TAG_NEGATE = [false => '', true => 'NOT '],
        WHERE_TAG_SEPARATOR = PHP_EOL . "\t %s ";

    /**
     * COLUMNS clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseColumns(RequestInterface $request, QueryInterface $query) {
        // Selected columns
        if ($columns = $request->getColumns()) {
            $list = [];

            foreach ($columns as $alias => $identifier) {
                $list[] = $this->nameToString($this->identifierToString($identifier, $query), is_numeric($alias) ? null : $alias);
            }

            $query->appendString(implode(static::LIST_SEPARATOR_MULTI_LINE, $list) . PHP_EOL);

            return;
        }

        // All
        $query->appendWord(static::ALL_COLUMNS);
    }

    /**
     * FROM clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseFrom(RequestInterface $request, QueryInterface $query) {
        $query->appendClause(static::CLAUSE_FROM, $this->requestNameToString($request));
    }

    /**
     * HAVING clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseHaving(RequestInterface $request, QueryInterface $query) {
        if ($request->hasHaving()) {
            $query->appendString($this->whereToString(static::CLAUSE_HAVING, $request->getHaving(), $query));
        }
    }

    /**
     * GROUP BY clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseGroupBy(RequestInterface $request, QueryInterface $query) {
        $groupBy = [];

        foreach ($request->getGroupBy() as $value) {
            $groupBy[] = $this->identifierToString($value, $query);
        }

        if ($groupBy) {
            $query->appendClause(static::CLAUSE_GROUP_BY, implode(static::LIST_SEPARATOR, $groupBy));
        }
    }

    /**
     * JOIN clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseJoin(RequestInterface $request, QueryInterface $query) {
        foreach ($request->getJoins() as $join) {
            $query
            ->appendClause(static::CLAUSE_JOIN, $this->nameToString($join->getRepository()->getName(), $join->getAlias()))
            ->appendString($this->whereToString(static::CLAUSE_JOIN_ON, $join->getWhere(), $query));
        }
    }

    /**
     * LIMIT clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseLimit(RequestInterface $request, QueryInterface $query) {
        $query->appendString($this->limitsToString($request->getOffset(), $request->getLimit()));
    }

    /**
     * ORDER BY clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseOrderBy(RequestInterface $request, QueryInterface $query) {
        $query->appendString($this->orderByToString($request->getOrderBy(), $query));
    }

    /**
     * WHERE clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     */
    protected function clauseWhere(RequestInterface $request, QueryInterface $query) {
        if ($request->hasWhere()) {
            $query->appendString($this->whereToString(static::CLAUSE_WHERE, $request->getWhere(), $query));
        }
    }

    /**
     * Expression array string
     *
     * @param array $expression
     * @param QueryInterface $query
     * @return string
     */
    protected function expressionArrayToString(array $expression, QueryInterface $query): string {
        $result = [];
        foreach ($expression as $item) {
            $result[] = $this->expressionToString($item, $query);
        }

        return sprintf(static::MASK_ARRAY, implode(static::LIST_SEPARATOR, $result));
    }

    /**
     * Expression ID to string
     *
     * @param EntityIdInterface $expression
     * @param QueryInterface $query
     * @return string
     */
    protected function expressionIdToString(EntityIdInterface $expression, QueryInterface $query) {
        $expression = $expression->getArrayCopy();
        if (count($expression) > 1) {
            return $this->expressionArrayToString($expression, $query);
        }

        return $this->expressionToString(reset($expression), $query);
    }

    /**
     * Expression string
     *
     * @param number|string|array|ExpressionInterface|RequestInterface $expression
     * @param QueryInterface $query
     * @return string
     */
    protected function expressionToString($expression, QueryInterface $query): string {
        // Expression
        if ($expression instanceof ExpressionInterface) {
            $query->addParameters($expression->getParameters());

            return $this->quoteExpression($expression->getExpression());
        }

        // Request
        if ($expression instanceof RequestInterface) {
//             return sprintf(static::MASK_NESTED, $this->requestToString($expression, $query));
return '';
        }

        // Array
        if (is_array($expression)) {
            return $this->expressionArrayToString($expression, $query);
        }

        // ID
        if ($expression instanceof EntityIdInterface) {
            return $this->expressionIdToString($expression, $query);
        }

        $query->addParameter($expression);

        return static::PLACEHOLDER;
    }

    /**
     * Identifier string
     *
     * @param number|string|array|ExpressionInterface|RequestInterface $identifier
     * @param QueryInterface $query
     * @return string
     */
    protected function identifierToString($identifier, QueryInterface $query): string {
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
     * @param int $offset
     * @param int|null $limit
     * @return string
     */
    protected function limitsToString(int $offset, $limit): string {
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
     * Make query
     *
     * @param string $statement
     * @param RequestInterface $request
     * @param string[] $tags
     * @return QueryInterface
     */
    protected function makeQuery(string $statement, RequestInterface $request, array $tags) {
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)->appendWord($statement);

        foreach ($tags as $tag) {
            $this->{static::METHOD_CLAUSE . $tag}($request, $query);
        }

        return $query;
    }

    /**
     * Name (with alias)
     *
     * @param string $name
     * @param string $alias
     * @return string
     */
    protected function nameToString(string $name, string $alias = null): string {
        if ($name !== $alias && !is_null($alias)) {
            return sprintf(static::MASK_ALIAS, $this->quoteIdentifier($name), $this->quoteIdentifier($alias));
        }

        return $this->quoteIdentifier($name);
    }

    /**
     * ORDER BY string
     *
     * @param array $orderBy
     * @param QueryInterface $query
     * @return string
     */
    protected function orderByToString(array $orderBy, QueryInterface $query): string {
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

    /**
     * Quote array identifier
     *
     * @param array $identifier
     * @return string
     */
    protected function quoteArrayIdentifier(array $identifier) {
        if (count($identifier) > 1) {
            return sprintf(static::MASK_ARRAY, implode(static::LIST_SEPARATOR, array_map([$this, 'quoteIdentifier'], $identifier)));
        }

        return $this->quoteIdentifier(reset($identifier));
    }

    /**
     * Quote expression (detects identifier-only expressions)
     *
     * @param string $expression
     * @return string
     */
    protected function quoteExpression(string $expression): string {
        $trimmed = trim($expression);

        // There is no terminal characters in the trimmed
        if (strcspn($trimmed, static::EXPRESSION_TERMINALS) === strlen($trimmed)) {
            return $this->quoteIdentifier($trimmed);
        }

        // Complex expression
        return $expression;
    }

    /**
     * Quote identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function quoteIdentifier(string $identifier): string {
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
     *
     * @param RequestInterface $request
     * @return string
     */
    protected function requestNameToString(RequestInterface $request): string {
        return $this->nameToString($request->getRepository()->getName(), $request->getAlias());
    }

    /**
     * Request string
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return string
     */
    protected function requestToString(RequestInterface $request, QueryInterface $query): string {
        $selectQuery = $this->select($request);

        $query->addParameters($selectQuery->getParameters());

        return $selectQuery->getText();
    }

    /**
     * Where BETWEEN tag
     *
     * @param BetweenTag $tag
     * @param QueryInterface $query
     * @return string
     */
    protected function whereTagBetween(BetweenTag $tag, QueryInterface $query): string {
        return $this->identifierToString($tag->getIdentifier(), $query) . ' ' . static::WHERE_TAG_NEGATE[$tag->isNegated()] . sprintf(static::MASK_BETWEEN, $this->expressionToString($tag->getMin(), $query)
            , $this->expressionToString($tag->getMax(), $query));
    }

    /**
     * Where compare tag
     *
     * @param CompareTag $tag
     * @param QueryInterface $query
     * @return string
     */
    protected function whereTagCompare(CompareTag $tag, QueryInterface $query): string {
        return static::WHERE_TAG_NEGATE[$tag->isNegated()] . $this->expressionToString($tag->getLeft(), $query) . ' ' . $tag->getOperator() . ' ' . $this->expressionToString($tag->getRight(), $query);
    }

    /**
     * Where EXISTS tag
     *
     * @param ExistsTag $tag
     * @param QueryInterface $query
     * @return string
     */
    protected function whereTagExists(ExistsTag $tag, QueryInterface $query): string {
        return static::WHERE_TAG_NEGATE[$tag->isNegated()] . sprintf(static::MASK_EXISTS, $this->requestToString($tag->getRequest(), $query));
    }

    /**
     * Where IN tag
     *
     * @param InTag $tag
     * @param QueryInterface $query
     * @return string
     */
    protected function whereTagIn(InTag $tag, QueryInterface $query): string {
        $values = $tag->getValues();

        return $this->identifierToString($tag->getIdentifier(), $query) . ' ' . static::WHERE_TAG_NEGATE[$tag->isNegated()] . sprintf(static::MASK_IN, $this->expressionToString($values, $query));
    }

    /**
     * Where null tag
     *
     * @param NullTag $tag
     * @param QueryInterface $query
     * @return string
     */
    protected function whereTagNull(NullTag $tag, QueryInterface $query): string {
        return $this->identifierToString($tag->getIdentifier(), $query) . ' ' . static::WHERE_TAG_IS_NULL[!$tag->isNegated()];
    }

    /**
     * Where nested tag
     *
     * @param WhereTag $tag
     * @param QueryInterface $query
     * @return string
     */
    protected function whereTagWhere(WhereTag $tag, QueryInterface $query): string {
        return sprintf(static::MASK_NESTED_MULTI_LINE, $this->whereToString('', $tag->getWhere(), $query));
    }

    /**
     * Where to string
     *
     * @param string $clause
     * @param WhereInterface $where
     * @param QueryInterface $query
     * @return string
     */
    protected function whereToString(string $clause, WhereInterface $where, QueryInterface $query): string {
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