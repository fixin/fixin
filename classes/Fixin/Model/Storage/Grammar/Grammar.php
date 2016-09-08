<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Resource;

abstract class Grammar extends Resource implements GrammarInterface {

    const ADD_COLUMN_NAMES = 'columnNames';
    const ADD_COLUMNS = 'columns';
    const ADD_FROM = 'from';
    const ADD_GROUP_BY = 'groupBy';
    const ADD_HAVING = 'having';
    const ADD_INTO = 'into';
    const ADD_JOIN = 'join';
    const ADD_LIMIT = 'limit';
    const ADD_ORDER_BY = 'orderBy';
    const ADD_WHERE = 'where';
    const CLAUSE_FROM = 'FROM';
    const CLAUSE_GROUP_BY = 'GROUP BY';
    const CLAUSE_HAVING = 'HAVING';
    const CLAUSE_INTO = 'INTO';
    const CLAUSE_JOIN = 'JOIN';
    const CLAUSE_JOIN_ON = "\tON";
    const CLAUSE_LIMIT = 'LIMIT';
    const CLAUSE_ORDER_BY = 'ORDER BY';
    const CLAUSE_SELECT = 'SELECT';
    const CLAUSE_VALUES = 'VALUES';
    const CLAUSE_WHERE = 'WHERE';
    const IDENTIFIER_QUOTE_CLOSE ="`";
    const IDENTIFIER_QUOTE_OPEN ="`";
    const EXPRESSION_TERMINALS = "\n\r\t '\"`()[]+-*/<>!=&|^,?@";
    const LIST_SEPARATOR = ', ';
    const LIST_SEPARATOR_MULTI_LINE = ',' . PHP_EOL . "\t";
    const MASK_ALIAS = '%s AS %s';
    const MASK_COLUMN_NAMES = "\t(%s)";
    const MASK_EXISTS = 'SELECT EXISTS(%s)';
    const MASK_ORDER_BY = '%s %s';
    const MASK_VALUES = '(%s)';
    const METHOD_CLAUSE = 'clause';
    const METHOD_WHERE_TAG = 'whereTag';
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESCENDING = 'DESC';
    const PROTOTYPE_QUERY = 'Base\Query\Query';
    const STATEMENT_DELETE = 'DELETE';
    const STATEMENT_INSERT = 'INSERT';
    const STATEMENT_SELECT = [false => 'SELECT', true => 'SELECT DISTINCT'];
    const STATEMENT_UPDATE = 'UPDATE';
    const TAG_IS_NULL = [false => 'IS NOT NULL', true => 'IS NULL'];
    const TAG_NEGATE = [false => '', true => 'NOT'];
    const TAG_SEPARATOR = PHP_EOL . "\t";

    /**
     * Name with alias (if needed)
     *
     * @param string $name
     * @param string $alias
     * @return string
     */
    protected function aliasedNameString(string $name, string $alias = null): string {
        if ($name !== $alias && !is_null($alias)) {
            return sprintf(static::MASK_ALIAS, $this->quoteIdentifier($name), $this->quoteIdentifier($alias));
        }

        return $this->quoteIdentifier($name);
    }

    /**
     * Append where
     *
     * @param string $clause
     * @param WhereInterface $where
     * @param QueryInterface $query
     */
    protected function appendWhere(string $clause, WhereInterface $where, QueryInterface $query) {
        if ($tags = $where->getTags()) {
            $query->appendWord($clause);

            foreach ($tags as $index => $tag) {
                if ($index) {
                    $query->appendWord(static::TAG_SEPARATOR . strtoupper($tag->getJoin()));
                }

                $class = get_class($tag);
                $shortName = substr($class, strrpos($class, '\\') + 1, -3);

                $this->{static::METHOD_WHERE_TAG . $shortName}($tag, $query);
            }

            $query->appendString(PHP_EOL);
        }
    }

    /**
     * Column names clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseColumnNames(RequestInterface $request, QueryInterface $query): self {
        if ($columns = $request->getColumns()) {
            $list = [];
            foreach ($columns as $alias => $identifier) {
                $list[] = $this->identifierString(is_numeric($alias) ? $identifier : $alias, $query);
            }

            $query->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, $list)) . PHP_EOL);
        }

        return $this;
    }

    /**
     * COLUMNS clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseColumns(RequestInterface $request, QueryInterface $query): self {
        // Selected columns
        if ($columns = $request->getColumns()) {
            $list = [];
            foreach ($columns as $alias => $identifier) {
                $list[] = $this->aliasedNameString($this->identifierString($identifier, $query), is_numeric($alias) ? null : $alias);
            }

            $query->appendString(implode(static::LIST_SEPARATOR_MULTI_LINE, $list) . PHP_EOL);

            return $this;
        }

        // All
        $query->appendWord('*');

        return $this;
    }

    /**
     * FROM clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseFrom(RequestInterface $request, QueryInterface $query): self {
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
    protected function clauseGroupBy(RequestInterface $request, QueryInterface $query): self {
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
    protected function clauseHaving(RequestInterface $request, QueryInterface $query): self {
        if ($request->hasHaving()) {
            $this->appendWhere(static::CLAUSE_HAVING, $request->getHaving(), $query);
        }

        return $this;
    }

    /**
     * INTO clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseInto(RequestInterface $request, QueryInterface $query): self {
        $query->appendClause(static::CLAUSE_INTO, $this->aliasedNameString($request->getRepository()->getName(), $request->getAlias()));

        return $this;
    }

    /**
     * JOIN clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseJoin(RequestInterface $request, QueryInterface $query): self {
        foreach ($request->getJoins() as $join) {
            $query->appendClause(static::CLAUSE_JOIN, $this->aliasedNameString($join->getRepository()->getName(), $join->getAlias()));

            $this->appendWhere(static::CLAUSE_JOIN_ON, $join->getWhere(), $query);
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
    protected function clauseLimit(RequestInterface $request, QueryInterface $query): self {
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
     * ORDER BY clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseOrderBy(RequestInterface $request, QueryInterface $query): self {
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
     * WHERE clause
     *
     * @param RequestInterface $request
     * @param QueryInterface $query
     * @return self
     */
    protected function clauseWhere(RequestInterface $request, QueryInterface $query): self {
        if ($request->hasWhere()) {
            $this->appendWhere(static::CLAUSE_WHERE, $request->getWhere(), $query);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::delete($request)
     */
    public function delete(RequestInterface $request): QueryInterface {
        return $this->makeQuery(static::STATEMENT_DELETE, $request, [static::ADD_FROM, static::ADD_WHERE, static::ADD_ORDER_BY, static::ADD_LIMIT]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::exists($request)
     */
    public function exists(RequestInterface $request): QueryInterface {
        $selectQuery = $this->select($request);

        $query= $this->container->clonePrototype(static::PROTOTYPE_QUERY)
        ->appendString(sprintf(static::MASK_EXISTS, $selectQuery->getText()))
        ->addParameters($selectQuery->getParameters());

        echo $query;
        die;
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

            return $this->quoteExpression($expression->getExpression());
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
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::insert($repository, $set)
     */
    public function insert(RepositoryInterface $repository, array $set): QueryInterface {
        return $this->insertMultiple($repository, [$set]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): QueryInterface {
        $query = $this->makeQuery(static::STATEMENT_INSERT, $request, [static::ADD_INTO, static::ADD_COLUMN_NAMES]);
        $selectQuery = $this->select($request);

        return $query
        ->appendString($selectQuery->getText())
        ->addParameters($selectQuery->getParameters());
    }

    /**
     * Insert rows
     *
     * @param RepositoryInterface $repository
     * @param array $rows
     * @return QueryInterface
     */
    protected function insertMultiple(RepositoryInterface $repository, array $rows): QueryInterface {
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)
        ->appendWord(static::STATEMENT_INSERT)
        ->appendClause(static::CLAUSE_INTO, $this->quoteIdentifier($repository->getName()));

        // Columns
        $list = [];
        foreach (reset($rows) as $key => $value) {
            $list[] = $this->identifierString($key, $query);
        }

        $query->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, $list)) . PHP_EOL);

        // Rows
        $source = [];
        foreach ($rows as $set) {
            $list = [];
            foreach ($set as $value) {
                $list[] = $this->expressionString($value, $query);
            }

            $source[] = sprintf(static::MASK_VALUES, implode(static::LIST_SEPARATOR, $list));
        }

        return $query->appendClause(static::CLAUSE_VALUES, implode(static::LIST_SEPARATOR_MULTI_LINE, $source));
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
            $this->{static::METHOD_CLAUSE . $tag}($request, $query);
        }

        return $query;
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
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::select($request)
     */
    public function select(RequestInterface $request): QueryInterface {
        return $this->makeQuery(static::STATEMENT_SELECT[$request->isDistinctResult()], $request, [static::ADD_COLUMNS, static::ADD_FROM, static::ADD_JOIN, static::ADD_WHERE, static::ADD_GROUP_BY, static::ADD_HAVING, static::ADD_ORDER_BY, static::ADD_LIMIT]);

        // TODO: unions
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): QueryInterface {
        // TODO
    }

    /**
     * Where compare tag
     *
     * @param CompareTag $tag
     * @param QueryInterface $query
     */
    protected function whereTagCompare(CompareTag $tag, QueryInterface $query) {
        $query->appendString($this->expressionString($tag->getLeft(), $query) . ' ' . $tag->getOperator() . ' ' . $this->expressionString($tag->getRight(), $query));
    }

    /**
     * Where null tag
     *
     * @param NullTag $tag
     * @param QueryInterface $query
     */
    protected function whereTagNull(NullTag $tag, QueryInterface $query) {
        $query->appendString($this->identifierString($tag->getIdentifier(), $query) . ' ' . static::TAG_IS_NULL[!$tag->isNegated()]);
    }
}