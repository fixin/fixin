<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use DateTime;
use Fixin\Base\Sentence\SentenceInterface;
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
        PROTOTYPE_SENTENCE = 'Base\Sentence\Sentence',
        STATEMENT_DELETE = 'DELETE',
        STATEMENT_INSERT = 'INSERT',
        STATEMENT_SELECT = [false => 'SELECT', true => 'SELECT DISTINCT'],
        STATEMENT_UPDATE = 'UPDATE';

    protected function clauseColumns(RequestInterface $request, SentenceInterface $sentence): void
    {
        // Selected columns
        if ($columns = $request->getColumns()) {
            $list = [];

            foreach ($columns as $alias => $identifier) {
                $list[] = $this->nameToString($this->identifierToString($identifier, $sentence), is_numeric($alias) ? null : $alias);
            }

            $sentence->appendString(implode(static::LIST_SEPARATOR_MULTI_LINE, $list) . PHP_EOL);

            return;
        }

        // All
        $sentence->appendWord(static::ALL_COLUMNS);
    }

    protected function clauseFrom(RequestInterface $request, SentenceInterface $sentence): void
    {
        $sentence->appendClause(static::CLAUSE_FROM, $this->requestNameToString($request));
    }

    protected function clauseHaving(RequestInterface $request, SentenceInterface $sentence): void
    {
        if ($request->hasHaving()) {
            $sentence->appendString($this->whereToString(static::CLAUSE_HAVING, $request->getHaving(), $sentence));
        }
    }

    protected function clauseGroupBy(RequestInterface $request, SentenceInterface $sentence): void
    {
        $groupBy = [];

        foreach ($request->getGroupBy() as $value) {
            $groupBy[] = $this->identifierToString($value, $sentence);
        }

        if ($groupBy) {
            $sentence->appendClause(static::CLAUSE_GROUP_BY, implode(static::LIST_SEPARATOR, $groupBy));
        }
    }

    protected function clauseJoin(RequestInterface $request, SentenceInterface $sentence): void
    {
        foreach ($request->getJoins() as $join) {
            $sentence->appendClause(static::CLAUSE_JOIN, $this->nameToString($join->getRepository()->getName(), $join->getAlias()));
            if ($where = $join->getWhere()) {
                $sentence->appendString($this->whereToString(static::CLAUSE_JOIN_ON, $where, $sentence));
            }
        }
    }

    protected function clauseLimit(RequestInterface $request, SentenceInterface $sentence): void
    {
        $sentence->appendString($this->limitsToString($request->getOffset(), $request->getLimit()));
    }

    protected function clauseOrderBy(RequestInterface $request, SentenceInterface $sentence): void
    {
        $sentence->appendString($this->orderByToString($request->getOrderBy(), $sentence));
    }

    protected function clauseWhere(RequestInterface $request, SentenceInterface $sentence): void
    {
        if ($request->hasWhere()) {
            $sentence->appendString($this->whereToString(static::CLAUSE_WHERE, $request->getWhere(), $sentence));
        }
    }

    public function delete(RequestInterface $request): SentenceInterface
    {
        return $this->makeSentence(static::STATEMENT_DELETE, $request, [static::ADD_FROM, static::ADD_WHERE, static::ADD_ORDER_BY, static::ADD_LIMIT]);
    }

    public function exists(RequestInterface $request): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->container->clonePrototype(static::PROTOTYPE_SENTENCE);

        return $sentence->appendClause(static::STATEMENT_SELECT[false], sprintf(static::MASK_EXISTS, $this->requestToString($request, $sentence)));
    }

    public function getValueAsDateTime($value): ?DateTime
    {
        if (Numbers::isInt($value)) {
            return new DateTime($value);
        }

        return DateTime::createFromFormat(static::DATETIME_FORMAT, $value) ?: null;
    }

    public function insert(RepositoryInterface $repository, array $set): SentenceInterface
    {
        return $this->insertMultiple($repository, [$set]);
    }

    public function insertInto(RepositoryInterface $repository, RequestInterface $request): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->container->clonePrototype(static::PROTOTYPE_SENTENCE);
        $sentence
            ->appendWord(static::STATEMENT_INSERT)
            ->appendClause(static::CLAUSE_INTO, $this->quoteIdentifier($repository->getName()));

        // Columns
        if ($columns = $request->getColumns()) {
            $list = [];
            foreach ($columns as $alias => $identifier) {
                $list[] = $this->identifierToString(is_numeric($alias) ? $identifier : $alias, $sentence);
            }

            $sentence->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, $list)));
        }

        // Select
        $sentence->appendString($this->requestToString($request, $sentence));

        return $sentence;
    }

    public function insertMultiple(RepositoryInterface $repository, array $rows): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->container->clonePrototype(static::PROTOTYPE_SENTENCE);
        $sentence
            ->appendWord(static::STATEMENT_INSERT)
            ->appendClause(static::CLAUSE_INTO, $this->quoteIdentifier($repository->getName()));

        $columnNames = [];
        foreach (array_keys(reset($rows)) as $identifier) {
            $columnNames[] = $this->identifierToString($identifier, $sentence);
        }

        $sentence->appendString(sprintf(static::MASK_COLUMN_NAMES, implode(static::LIST_SEPARATOR, $columnNames)));

        $source = [];
        foreach ($rows as $set) {
            $values = [];
            foreach ($set as $value) {
                $values[] = $this->expressionToString($value, $sentence);
            }

            $source[] = sprintf(static::MASK_VALUES, implode(static::LIST_SEPARATOR, $values));
        }

        return $sentence->appendClause(static::CLAUSE_VALUES, implode(static::LIST_SEPARATOR_MULTI_LINE, $source));
    }

    protected function makeSentence(string $statement, RequestInterface $request, array $tags): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->container->clonePrototype(static::PROTOTYPE_SENTENCE);
        $sentence->appendWord($statement);

        foreach ($tags as $tag) {
            $this->{static::METHOD_CLAUSE . $tag}($request, $sentence);
        }

        return $sentence;
    }

    public function select(RequestInterface $request): SentenceInterface
    {
        $sentence = $this->makeSentence(static::STATEMENT_SELECT[$request->isDistinctResult()], $request, [static::ADD_COLUMNS, static::ADD_FROM, static::ADD_JOIN, static::ADD_WHERE, static::ADD_GROUP_BY, static::ADD_HAVING, static::ADD_ORDER_BY, static::ADD_LIMIT]);

        // Unions
        if ($unions = $request->getUnions()) {
            $sentence->applyMask(static::MASK_UNION_FIRST);

            foreach ($unions as $union) {
                $sentence->appendString(sprintf(static::MASK_UNION, static::CLAUSE_UNION[$union->getType()], $this->requestToString($union->getRequest(), $sentence)));
            }

            // Union order by
            $sentence->appendString($this->orderByToString($request->getUnionOrderBy(), $sentence));

            // Union offset and limit
            $sentence->appendString($this->limitsToString($request->getUnionOffset(), $request->getUnionLimit()));
        }

        return $sentence;
    }

    public function update(array $set, RequestInterface $request): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->container->clonePrototype(static::PROTOTYPE_SENTENCE);
        $sentence->appendClause(static::STATEMENT_UPDATE, $this->requestNameToString($request));

        // Set
        $list = [];
        foreach ($set as $key => $value) {
            $list[] = $this->identifierToString($key, $sentence) . ' = ' . $this->expressionToString($value, $sentence);
        }

        $sentence->appendClause(static::CLAUSE_SET, implode(static::LIST_SEPARATOR_MULTI_LINE, $list));

        // Where
        $this->clauseWhere($request, $sentence);

        return $sentence;
    }
}
