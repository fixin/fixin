<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use DateTime;
use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\UnionInterface;
use Fixin\Support\Numbers;

abstract class Grammar extends GrammarBase
{
    protected const
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
        MASK_COLUMN_NAMES = "\t(%s)" . PHP_EOL,
        MASK_UNION = '%s' . PHP_EOL . '(%s)' . PHP_EOL,
        MASK_UNION_FIRST = '(%s)' . PHP_EOL,
        MASK_VALUES = '(%s)',
        METHOD_CLAUSE = 'clause',
        PROTOTYPE_QUERY = 'Base\Query\Query',
        STATEMENT_DELETE = 'DELETE',
        STATEMENT_INSERT = 'INSERT',
        STATEMENT_SELECT = [false => 'SELECT', true => 'SELECT DISTINCT'],
        STATEMENT_UPDATE = 'UPDATE';

    protected function clauseColumns(RequestInterface $request, QueryInterface $query): void
    {
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

    protected function clauseFrom(RequestInterface $request, QueryInterface $query): void
    {
        $query->appendClause(static::CLAUSE_FROM, $this->requestNameToString($request));
    }

    protected function clauseHaving(RequestInterface $request, QueryInterface $query): void
    {
        if ($request->hasHaving()) {
            $query->appendString($this->whereToString(static::CLAUSE_HAVING, $request->getHaving(), $query));
        }
    }

    protected function clauseGroupBy(RequestInterface $request, QueryInterface $query): void
    {
        $groupBy = [];

        foreach ($request->getGroupBy() as $value) {
            $groupBy[] = $this->identifierToString($value, $query);
        }

        if ($groupBy) {
            $query->appendClause(static::CLAUSE_GROUP_BY, implode(static::LIST_SEPARATOR, $groupBy));
        }
    }

    protected function clauseJoin(RequestInterface $request, QueryInterface $query): void
    {
        foreach ($request->getJoins() as $join) {
            $query->appendClause(static::CLAUSE_JOIN, $this->nameToString($join->getRepository()->getName(), $join->getAlias()));
            if ($where = $join->getWhere()) {
                $query->appendString($this->whereToString(static::CLAUSE_JOIN_ON, $where, $query));
            }
        }
    }

    protected function clauseLimit(RequestInterface $request, QueryInterface $query): void
    {
        $query->appendString($this->limitsToString($request->getOffset(), $request->getLimit()));
    }

    protected function clauseOrderBy(RequestInterface $request, QueryInterface $query): void
    {
        $query->appendString($this->orderByToString($request->getOrderBy(), $query));
    }

    protected function clauseWhere(RequestInterface $request, QueryInterface $query): void
    {
        if ($request->hasWhere()) {
            $query->appendString($this->whereToString(static::CLAUSE_WHERE, $request->getWhere(), $query));
        }
    }

    public function delete(RequestInterface $request): QueryInterface
    {
        return $this->makeQuery(static::STATEMENT_DELETE, $request, [static::ADD_FROM, static::ADD_WHERE, static::ADD_ORDER_BY, static::ADD_LIMIT]);
    }

    public function exists(RequestInterface $request): QueryInterface
    {
        /** @var QueryInterface $query */
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY);

        return $query->appendClause(static::STATEMENT_SELECT[false], sprintf(static::MASK_EXISTS, $this->requestToString($request, $query)));
    }

    public function getValueAsDateTime($value): ?DateTime
    {
        if (Numbers::isInt($value)) {
            return new DateTime($value);
        }

        return DateTime::createFromFormat(static::DATETIME_FORMAT, $value) ?: null;
    }

    public function insert(RepositoryInterface $repository, array $set): QueryInterface
    {
        return $this->insertMultiple($repository, [$set]);
    }

    public function insertInto(RepositoryInterface $repository, RequestInterface $request): QueryInterface
    {
        /** @var QueryInterface $query */
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY);
        $query
            ->appendWord(static::STATEMENT_INSERT)
            ->appendClause(static::CLAUSE_INTO, $this->quoteIdentifier($repository->getName()));

        // Columns
        if ($columns = $request->getColumns()) {
            $list = [];
            foreach ($columns as $alias => $identifier) {
                $list[] = $this->identifierToString(is_numeric($alias) ? $identifier : $alias, $query);
            }

            $query->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, $list)));
        }

        // Select
        $query->appendString($this->requestToString($request, $query));

        return $query;
    }

    public function insertMultiple(RepositoryInterface $repository, array $rows): QueryInterface
    {
        /** @var QueryInterface $query */
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY);
        $query
            ->appendWord(static::STATEMENT_INSERT)
            ->appendClause(static::CLAUSE_INTO, $this->quoteIdentifier($repository->getName()));

        $columnNames = [];
        foreach (array_keys(reset($rows)) as $identifier) {
            $columnNames[] = $this->identifierToString($identifier, $query);
        }

        $query->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, $columnNames)));

        $source = [];
        foreach ($rows as $set) {
            $values = [];
            foreach ($set as $value) {
                $values[] = $this->expressionToString($value, $query);
            }

            $source[] = sprintf(static::MASK_VALUES, implode(static::LIST_SEPARATOR, $values));
        }

        return $query->appendClause(static::CLAUSE_VALUES, implode(static::LIST_SEPARATOR_MULTI_LINE, $source));
    }

    protected function makeQuery(string $statement, RequestInterface $request, array $tags): QueryInterface
    {
        /** @var QueryInterface $query */
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY);
        $query->appendWord($statement);

        foreach ($tags as $tag) {
            $this->{static::METHOD_CLAUSE . $tag}($request, $query);
        }

        return $query;
    }

    public function select(RequestInterface $request): QueryInterface
    {
        $query = $this->makeQuery(static::STATEMENT_SELECT[$request->isDistinctResult()], $request, [static::ADD_COLUMNS, static::ADD_FROM, static::ADD_JOIN, static::ADD_WHERE, static::ADD_GROUP_BY, static::ADD_HAVING, static::ADD_ORDER_BY, static::ADD_LIMIT]);

        // Unions
        if ($unions = $request->getUnions()) {
            $query->applyMask(static::MASK_UNION_FIRST);

            foreach ($unions as $union) {
                $query->appendString(sprintf(static::MASK_UNION, static::CLAUSE_UNION[$union->getType()], $this->requestToString($union->getRequest(), $query)));
            }

            // Union order by
            $query->appendString($this->orderByToString($request->getUnionOrderBy(), $query));

            // Union offset and limit
            $query->appendString($this->limitsToString($request->getUnionOffset(), $request->getUnionLimit()));
        }

        return $query;
    }

    public function update(array $set, RequestInterface $request): QueryInterface
    {
        /** @var QueryInterface $query */
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY);
        $query->appendClause(static::STATEMENT_UPDATE, $this->requestNameToString($request));

        // Set
        $list = [];
        foreach ($set as $key => $value) {
            $list[] = $this->identifierToString($key, $query) . ' = ' . $this->expressionToString($value, $query);
        }

        $query->appendClause(static::CLAUSE_SET, implode(static::LIST_SEPARATOR_MULTI_LINE, $list));

        // Where
        $this->clauseWhere($request, $query);

        return $query;
    }

}
