<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage\Grammar;

use Fixin\Base\Sentence\SentenceInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\JoinInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\UnionInterface;

abstract class SqlGrammar extends Grammar
{
    protected const
        ALL_COLUMNS = '*',
        METHOD_PREFIX_CLAUSE = 'clause',
        COLUMN_NAMES_MASK = "\t(%s)" . PHP_EOL,
        COLUMNS = 'columns',
        DELETE_STATEMENT = 'DELETE',
        FROM = 'from',
        FROM_CLAUSE = 'FROM',
        GROUP_BY = 'groupBy',
        GROUP_BY_CLAUSE = 'GROUP BY',
        HAVING = 'having',
        HAVING_CLAUSE = 'HAVING',
        INSERT_STATEMENT = 'INSERT',
        INTO_CLAUSE = 'INTO',
        JOIN = 'join',
        JOIN_CLAUSE = [JoinInterface::TYPE_CROSS => 'CROSS JOIN', JoinInterface::TYPE_INNER => 'INNER JOIN', JoinInterface::TYPE_LEFT => 'LEFT JOIN', JoinInterface::TYPE_RIGHT => 'RIGHT JOIN'],
        JOIN_ON_CLAUSE = "\tON",
        LIMIT = 'limit',
        ORDER_BY = 'orderBy',
        SELECT_STATEMENT = [false => 'SELECT', true => 'SELECT DISTINCT'],
        SENTENCE_PROTOTYPE = 'Base\Sentence\Sentence',
        SET_CLAUSE = 'SET',
        UNION_CLAUSE = [UnionInterface::TYPE_NORMAL => 'UNION', UnionInterface::TYPE_ALL => 'UNION ALL'],
        UNION_FIRST_MASK = '(%s)' . PHP_EOL,
        UNION_MASK = '%s' . PHP_EOL . '(%s)' . PHP_EOL,
        UPDATE_STATEMENT = 'UPDATE',
        VALUES_CLAUSE = 'VALUES',
        VALUES_MASK = '(%s)',
        WHERE = 'where',
        WHERE_CLAUSE = 'WHERE';

    protected function clauseColumns(RequestInterface $request, SentenceInterface $sentence): void
    {
        // Selected columns
        if ($columns = $request->getColumns()) {
            $list = [];

            foreach ($columns as $alias => $identifier) {
                $list[] = $this->nameToString($this->identifierToString($identifier, $sentence), is_numeric($alias) ? null : $alias);
            }

            $sentence->appendString(implode(static::MULTI_LINE_LIST_SEPARATOR, $list) . PHP_EOL);

            return;
        }

        // All
        $sentence->appendWord(static::ALL_COLUMNS);
    }

    protected function clauseFrom(RequestInterface $request, SentenceInterface $sentence): void
    {
        $sentence->appendClause(static::FROM_CLAUSE, $this->requestNameToString($request));
    }

    protected function clauseHaving(RequestInterface $request, SentenceInterface $sentence): void
    {
        if ($request->hasHaving()) {
            $sentence->appendString($this->whereToString(static::HAVING_CLAUSE, $request->getHaving(), $sentence));
        }
    }

    protected function clauseGroupBy(RequestInterface $request, SentenceInterface $sentence): void
    {
        $groupBy = [];

        foreach ($request->getGroupBy() as $value) {
            $groupBy[] = $this->identifierToString($value, $sentence);
        }

        if ($groupBy) {
            $sentence->appendClause(static::GROUP_BY_CLAUSE, implode(static::LIST_SEPARATOR, $groupBy));
        }
    }

    protected function clauseJoin(RequestInterface $request, SentenceInterface $sentence): void
    {
        foreach ($request->getJoins() as $join) {
            $sentence->appendClause(static::JOIN_CLAUSE[$join->getType()], $this->nameToString($join->getRepository()->getName(), $join->getAlias()));
            if ($where = $join->getWhere()) {
                $sentence->appendString($this->whereToString(static::JOIN_ON_CLAUSE, $where, $sentence));
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
            $sentence->appendString($this->whereToString(static::WHERE_CLAUSE, $request->getWhere(), $sentence));
        }
    }

    public function delete(RequestInterface $request): SentenceInterface
    {
        return $this->makeSentence(static::DELETE_STATEMENT, $request, [static::FROM, static::JOIN, static::WHERE, static::ORDER_BY, static::LIMIT]);
    }

    public function insertInto(RepositoryInterface $repository, RequestInterface $request): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->resourceManager->clone(static::SENTENCE_PROTOTYPE, SentenceInterface::class);
        $sentence
            ->appendWord(static::INSERT_STATEMENT)
            ->appendClause(static::INTO_CLAUSE, $this->quoteIdentifier($repository->getName()));

        // Columns
        if ($columns = $request->getColumns()) {
            $list = [];
            foreach ($columns as $alias => $identifier) {
                $list[] = $this->identifierToString(is_numeric($alias) ? $identifier : $alias, $sentence);
            }

            $sentence->appendString(sprintf(static::COLUMN_NAMES_MASK, implode(static::LIST_SEPARATOR, $list)));
        }

        // Select
        $sentence->appendString($this->requestToString($request, $sentence));

        return $sentence;
    }

    public function insertMultiple(RepositoryInterface $repository, array $rows): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->resourceManager->clone(static::SENTENCE_PROTOTYPE, SentenceInterface::class);
        $sentence
            ->appendWord(static::INSERT_STATEMENT)
            ->appendClause(static::INTO_CLAUSE, $this->quoteIdentifier($repository->getName()));

        $columnNames = [];
        foreach (array_keys(reset($rows)) as $identifier) {
            $columnNames[] = $this->identifierToString($identifier, $sentence);
        }

        $sentence->appendString(sprintf(static::COLUMN_NAMES_MASK, implode(static::LIST_SEPARATOR, $columnNames)));

        $source = [];
        foreach ($rows as $set) {
            $values = [];
            foreach ($set as $value) {
                $values[] = $this->expressionToString($value, $sentence);
            }

            $source[] = sprintf(static::VALUES_MASK, implode(static::LIST_SEPARATOR, $values));
        }

        return $sentence->appendClause(static::VALUES_CLAUSE, implode(static::MULTI_LINE_LIST_SEPARATOR, $source));
    }

    protected function makeSentence(string $statement, RequestInterface $request, array $tags): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->resourceManager->clone(static::SENTENCE_PROTOTYPE, SentenceInterface::class);
        $sentence->appendWord($statement);

        foreach ($tags as $tag) {
            $this->{static::METHOD_PREFIX_CLAUSE . $tag}($request, $sentence);
        }

        return $sentence;
    }

    public function select(RequestInterface $request): SentenceInterface
    {
        $sentence = $this->makeSentence(static::SELECT_STATEMENT[$request->isDistinctResult()], $request, [static::COLUMNS, static::FROM, static::JOIN, static::WHERE, static::GROUP_BY, static::HAVING, static::ORDER_BY, static::LIMIT]);

        // Unions
        if ($unions = $request->getUnions()) {
            $sentence->applyMask(static::UNION_FIRST_MASK);

            foreach ($unions as $union) {
                $sentence->appendString(sprintf(static::UNION_MASK, static::UNION_CLAUSE[$union->getType()], $this->requestToString($union->getRequest(), $sentence)));
            }

            // Union order by
            $sentence->appendString($this->orderByToString($request->getUnionOrderBy(), $sentence));

            // Union offset and limit
            $sentence->appendString($this->limitsToString($request->getUnionOffset(), $request->getUnionLimit()));
        }

        return $sentence;
    }

    public function selectExistsValue(RequestInterface $request): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->resourceManager->clone(static::SENTENCE_PROTOTYPE, SentenceInterface::class);

        return $sentence->appendClause(static::SELECT_STATEMENT[false], sprintf(static::EXISTS_MASK, $this->requestToString($request, $sentence)));
    }

    public function update(array $set, RequestInterface $request): SentenceInterface
    {
        /** @var SentenceInterface $sentence */
        $sentence = $this->resourceManager->clone(static::SENTENCE_PROTOTYPE, SentenceInterface::class);
        $sentence->appendClause(static::UPDATE_STATEMENT, $this->requestNameToString($request));

        // Set
        $list = [];
        foreach ($set as $key => $value) {
            $list[] = $this->identifierToString($key, $sentence) . ' = ' . $this->expressionToString($value, $sentence);
        }

        $sentence->appendClause(static::SET_CLAUSE, implode(static::MULTI_LINE_LIST_SEPARATOR, $list));

        // Where
        $this->clauseWhere($request, $sentence);

        return $sentence;
    }
}
