<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Resource;

class MysqlGrammar extends Resource implements GrammarInterface {

    const ADD_FROM = 'from';
    const ADD_GROUP_BY = 'groupBy';
    const ADD_HAVING = 'having';
    const ADD_JOIN = 'join';
    const ADD_LIMIT = 'limit';
    const ADD_ORDER_BY = 'orderBy';
    const ADD_WHERE = 'where';
    const CLAUSE_FROM = 'FROM';
    const CLAUSE_GROUP_BY = 'GROUP BY';
    const CLAUSE_HAVING = 'HAVING';
    const CLAUSE_JOIN = 'JOIN';
    const CLAUSE_JOIN_ON = 'ON';
    const CLAUSE_LIMIT = 'LIMIT';
    const CLAUSE_ORDER_BY = 'ORDER BY';
    const CLAUSE_WHERE = 'WHERE';
    const IDENTIFIER_QUOTE = '`';
    const IDENTIFIER_SEPARATOR = '.';
    const LIST_SEPARATOR = ', ';
    const MASK_ALIAS = '%s AS %s';
    const MASK_ORDER_BY = '%s %s';
    const METHOD_CLAUSE = 'Clause';
    const METHOD_WHERE_TAG = 'where';
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESCENDING = 'DESC';
    const PROTOTYPE_QUERY = 'Model\Storage\Pdo\Query';
    const STATEMENT_DELETE = 'DELETE';
    const STATEMENT_INSERT = 'INSERT';
    const STATEMENT_SELECT = 'SELECT';
    const STATEMENT_UPDATE = 'UPDATE';
    const TAG_IS_NULL = [false => 'IS NOT NULL', true => 'IS NULL'];
    const TAG_NEGATE = [false => '', true => 'NOT'];

    /**
     * Name with alias (if needed)
     *
     * @param string $name
     * @param string $alias
     * @return string
     */
    protected function aliasedNameString(string $name, string $alias): string {
        if ($name !== $alias) {
            return sprintf(static::MASK_ALIAS, $this->quoteIdentifier($name), $this->quoteIdentifier($alias));
        }

        return $this->quoteIdentifier($name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::delete($request)
     */
    public function delete(RequestInterface $request): QueryInterface {
        return $this->makeQuery(static::STATEMENT_DELETE, $request, [static::ADD_FROM, static::ADD_WHERE, static::ADD_ORDER_BY, static::ADD_LIMIT]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::exists($request)
     */
    public function exists(RequestInterface $request): QueryInterface {
        // TODO
    }

    /**
     * Expression string
     *
     * @param number|string|array|ExpressionInterface $expression
     * @param QueryInterface $query
     * @return string
     */
    protected function expressionString($expression, QueryInterface $query): string {
        // Expression
        if ($expression instanceof ExpressionInterface) {
            $query->addParameters($expression->getParameters());

            return $expression->getExpression();
        }

        $query->addParameter($expression);

        return '?';
    }

    /**
     * Identifier string
     *
     * @param number|string|ExpressionInterface $identifier
     * @param QueryInterface $query
     * @return string
     */
    protected function identifierString($identifier, QueryInterface $query): string {
        if ($identifier instanceof ExpressionInterface) {
            $query->addParameters($identifier->getParameters());

            $identifier = $identifier->getExpression();
        }

        return $this->quoteIdentifier($identifier);
    }

    /**
     * FROM clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function fromClause(RequestInterface $request, QueryInterface $query): self {
        $query->appendClause(static::CLAUSE_FROM, $this->aliasedNameString($request->getRepository()->getName(), $request->getAlias()));

        return $this;
    }

    /**
     * GROUP BY clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function groupByClause(RequestInterface $request, QueryInterface $query): self {
        $groupBy = [];

        foreach ($request->getGroupBy() as $value) {
            $groupBy[] = $this->identifierString($value, $query);
        }

        if ($groupBy) {
            $query->appendClause(static::CLAUSE_GROUP_BY, implode(static::LIST_SEPARATOR, $groupBy));
        }

        return $this;
    }

    /**
     * HAVING clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function havingClause(RequestInterface $request, QueryInterface $query): self {
        if ($request->hasHaving()) {
            $this->whereString(static::CLAUSE_HAVING, $request->getHaving(), $query);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::insert($repository, $set)
     */
    public function insert(RepositoryInterface $repository, array $set): QueryInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): QueryInterface {
        // TODO
    }

    /**
     * JOIN clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function joinClause(RequestInterface $request, QueryInterface $query): self {
        foreach ($request->getJoins() as $join) {
            $query->appendClause(static::CLAUSE_JOIN, $this->aliasedNameString($join->getRepository()->getName(), $join->getAlias()));

            $on = $this->whereString(static::CLAUSE_JOIN_ON, $join->getWhere(), $query);
        }

        return $this;
    }

    /**
     * LIMIT clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function limitClause(RequestInterface $request, QueryInterface $query): self {
        if ($limit = $request->getLimit()) {
            if ($offset = $request->getOffset()) {
                $limit = "$offset, $limit";
            }

            $query->appendClause(static::CLAUSE_LIMIT, $limit);
        }
        // Offset-only
        elseif ($offset = $request->getOffset()) {
            $query->appendClause(static::CLAUSE_LIMIT, $offset . ', ' . PHP_INT_MAX);
        }

        return $this;
    }

    /**
     * Make query
     *
     * @param string $statement
     * @param RequestInterface $request
     * @param array $tags
     * @return QueryInterface
     */
    protected function makeQuery(string $statement, RequestInterface $request, array $tags) {
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)->appendWord($statement);

        foreach ($tags as $tag) {
            $this->{$tag . static::METHOD_CLAUSE}($request, $query);
        }

        return $query;
    }

    /**
     * ORDER BY clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function orderByClause(RequestInterface $request, QueryInterface $query): self {
        $orderBy = [];

        foreach ($request->getOrderBy() as $key => $value) {
            if (is_numeric($key)) {
                $orderBy[] = $this->identifierString($value, $query);

                continue;
            }

            $orderBy[] = sprintf(static::MASK_ORDER_BY, $this->quoteIdentifier($key), strtoupper($value) === static::ORDER_DESCENDING ? static::ORDER_DESCENDING : static::ORDER_ASCENDING);
        }

        if ($orderBy) {
            $query->appendClause(static::CLAUSE_ORDER_BY, implode(static::LIST_SEPARATOR, $orderBy));
        }

        return $this;
    }

    /**
     * Quote expression
     *
     * @param string $expression
     * @return string
     */
    protected function quoteExpression(string $expression): string {
        return $expression;
    }

    /**
     * Quote identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function quoteIdentifier(string $identifier): string {
        $tags = explode(static::IDENTIFIER_SEPARATOR, $identifier);
        foreach ($tags as &$tag) {
            if ($tag[0] !== static::IDENTIFIER_QUOTE) {
                $tag = static::IDENTIFIER_QUOTE . $tag . static::IDENTIFIER_QUOTE;
            }
        }

        return implode(static::IDENTIFIER_SEPARATOR, $tags);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::select($request)
     */
    public function select(RequestInterface $request): QueryInterface {
        echo $this->makeQuery(static::STATEMENT_SELECT, $request, [static::ADD_FROM, static::ADD_JOIN, static::ADD_WHERE, static::ADD_GROUP_BY, static::ADD_HAVING, static::ADD_ORDER_BY, static::ADD_LIMIT]);

        // TODO: unions
        die;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): QueryInterface {
        // TODO
    }

    /**
     * WHERE clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function whereClause(RequestInterface $request, QueryInterface $query): self {
        if ($request->hasWhere()) {
            $this->whereString(static::CLAUSE_WHERE, $request->getWhere(), $query);
        }

        return $this;
    }

    /**
     * Where compare tag
     *
     * @param CompareTag $tag
     * @param QueryInterface $query
     */
    protected function whereCompareTag(CompareTag $tag, QueryInterface $query) {
        $query->appendString($this->expressionString($tag->getLeft(), $query) . ' ' . $tag->getOperator() . ' ' . $this->expressionString($tag->getRight(), $query));
    }

    /**
     * Where null tag
     *
     * @param NullTag $tag
     * @param QueryInterface $query
     */
    protected function whereNullTag(NullTag $tag, QueryInterface $query) {
        $query->appendString($this->identifierString($tag->getIdentifier(), $query) . ' ' . static::TAG_IS_NULL[!$tag->isNegated()]);
    }

    /**
     * Generate where string
     *
     * @param string $clause
     * @param WhereInterface $where
     * @param QueryInterface $query
     */
    protected function whereString(string $clause, WhereInterface $where, QueryInterface $query) {
        if ($tags = $where->getTags()) {
            $query->appendWord($clause);

            foreach ($tags as $index => $tag) {
                if ($index) {
                    $query->appendWord(PHP_EOL . strtoupper($tag->getJoin()));
                }

                $class = get_class($tag);
                $shortName = substr($class, strrpos($class, '\\') + 1);

                $this->{static::METHOD_WHERE_TAG . $shortName}($tag, $query);
            }

            $query->appendString(PHP_EOL);
        }
    }
}