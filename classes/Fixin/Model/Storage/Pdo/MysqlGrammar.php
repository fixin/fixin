<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\Resource;
use Fixin\Model\Request\ExpressionInterface;

class MysqlGrammar extends Resource implements GrammarInterface {

    const ADD_FROM = 'from';
    const ADD_GROUP_BY = 'groupBy';
    const ADD_HAVING = 'having';
    const ADD_JOIN = 'join';
    const ADD_LIMIT = 'limit';
    const ADD_ORDER_BY = 'orderBy';
    const ADD_WHERE = 'where';
    const ALIAS_MASK = '%s AS %s';
    const ASCENDING = 'ASC';
    const CLAUSE_FROM = 'FROM';
    const CLAUSE_GROUP_BY = 'GROUP BY';
    const CLAUSE_LIMIT = 'LIMIT';
    const CLAUSE_ORDER_BY = 'ORDER BY';
    const DESCENDING = 'DESC';
    const LIST_SEPARATOR = ', ';
    const ORDER_BY_MASK = '%s %s';
    const PROTOTYPE_QUERY = 'Model\Storage\Pdo\Query';
    const STATEMENT_DELETE = 'DELETE';
    const STATEMENT_INSERT = 'INSERT';
    const STATEMENT_SELECT = 'SELECT';
    const STATEMENT_UPDATE = 'UPDATE';

    /**
     * Name with alias (if needed)
     *
     * @param string $name
     * @param string $alias
     * @return string
     */
    protected function aliasedNameString(string $name, string $alias): string {
        if ($name !== $alias) {
            $name = sprintf(static::ALIAS_MASK, $name, $alias);
        }

        return $name;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\GrammarInterface::delete($request)
     */
    public function delete(RequestInterface $request): QueryInterface {
        return $this->makeQuery(static::STATEMENT_DELETE, $request, [static::ADD_FROM, static::ADD_WHERE, static::ADD_ORDER_BY, static::ADD_LIMIT]);
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
            $groupBy[] = $value instanceof ExpressionInterface ? $value->getExpression() : $value;
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
        // TODO

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
        // TODO

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
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)->appendText($statement . ' ');

        foreach ($tags as $tag) {
            $this->{$tag . 'Clause'}($request, $query);
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
                $orderBy[] = $value instanceof ExpressionInterface ? $value->getExpression() : $value;

                continue;
            }

            $orderBy[] = sprintf(static::ORDER_BY_MASK, $key, strtoupper($value) === static::DESCENDING ? static::DESCENDING : static::ASCENDING);
        }

        if ($orderBy) {
            $query->appendClause(static::CLAUSE_ORDER_BY, implode(static::LIST_SEPARATOR, $orderBy));
        }

        return $this;
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
        $where = '';

        return $this;
    }
}