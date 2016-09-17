<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;

abstract class Grammar extends GrammarBase {

    const
        MASK_COLUMN_NAMES = "\t(%s)" . PHP_EOL,
        MASK_UNION = '%s' . PHP_EOL . '(%s)' . PHP_EOL,
        MASK_UNION_FIRST = '(%s)' . PHP_EOL,
        MASK_VALUES = '(%s)',
        STATEMENT_DELETE = 'DELETE',
        STATEMENT_INSERT = 'INSERT',
        STATEMENT_SELECT = [false => 'SELECT', true => 'SELECT DISTINCT'],
        STATEMENT_UPDATE = 'UPDATE';

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
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY);

        return $query->appendClause(static::STATEMENT_SELECT[false], sprintf(static::MASK_EXISTS, $this->requestToString($request, $query)));
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
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)
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

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::insertMultiple($repository, $rows)
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): QueryInterface {
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)
        ->appendWord(static::STATEMENT_INSERT)
        ->appendClause(static::CLAUSE_INTO, $this->quoteIdentifier($repository->getName()));

        // Columns
        $query->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, array_map(function($identifier) use ($query) {
            return $this->identifierToString($identifier, $query);
        }, array_keys(reset($rows))))));

        // Rows
        $source = [];
        foreach ($rows as $set) {
            $source[] = sprintf(static::MASK_VALUES, implode(static::LIST_SEPARATOR, array_map(function($value) use ($query) {
                return $this->expressionToString($value, $query);
            }, $set)));
        }

        return $query->appendClause(static::CLAUSE_VALUES, implode(static::LIST_SEPARATOR_MULTI_LINE, $source));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::select($request)
     */
    public function select(RequestInterface $request): QueryInterface {
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

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Grammar\GrammarInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): QueryInterface {
        $query = $this->container->clonePrototype(static::PROTOTYPE_QUERY)
        ->appendClause(static::STATEMENT_UPDATE, $this->requestNameToString($request));

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