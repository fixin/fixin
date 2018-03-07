<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface RequestInterface extends PrototypeInterface
{
    public const
        REPOSITORY = 'repository';

    /**
     * Count
     *
     * @return int
     */
    public function count(): int;

    /**
     * Create expression
     *
     * @param string $expression
     * @param array $parameters
     * @return ExpressionInterface
     */
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface;

    /**
     * Add cross join
     *
     * @param RepositoryInterface $repository
     * @param string|null $alias
     * @return $this
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): RequestInterface;

    /**
     * Delete
     *
     * @return int
     */
    public function delete(): int;

    /**
     * Fetch entities
     *
     * @return EntitySetInterface
     */
    public function fetch(): EntitySetInterface;

    /**
     * Fetch column
     *
     * @param string|ExpressionInterface|RequestInterface $column
     * @return StorageResultInterface
     */
    public function fetchColumn($column): StorageResultInterface;

    /**
     * Determine if exists
     *
     * @return bool
     */
    public function fetchExistsValue(): bool;

    /**
     * Fetch first entity
     *
     * @return EntityInterface|null
     */
    public function fetchFirst() : ?EntityInterface;

    /**
     * Fetch raw data
     *
     * @return StorageResultInterface
     */
    public function fetchRawData(): StorageResultInterface;

    /**
     * Fetch value
     *
     * @param string|ExpressionInterface|RequestInterface $column
     * @return mixed
     */
    public function fetchValue($column);

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns(): array;

    /**
     * Get group by parameters
     *
     * @return array
     */
    public function getGroupBy(): array;

    /**
     * Get having part
     *
     * @return WhereInterface
     */
    public function getHaving(): WhereInterface;

    /**
     * Get join parts
     *
     * @return JoinInterface[]
     */
    public function getJoins(): array;

    /**
     * Get limit
     *
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Get order by parameters
     *
     * @return array
     */
    public function getOrderBy(): array;

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Get union limit
     *
     * @return int|null
     */
    public function getUnionLimit(): ?int;

    /**
     * Get union offset
     *
     * @return int
     */
    public function getUnionOffset(): int;

    /**
     * Get union order by
     *
     * @return array
     */
    public function getUnionOrderBy(): array;

    /**
     * Get union parts
     *
     * @return UnionInterface[]
     */
    public function getUnions(): array;

    /**
     * Get where part
     *
     * @return WhereInterface
     */
    public function getWhere(): WhereInterface;

    /**
     * Determine if has having part
     *
     * @return bool
     */
    public function hasHaving(): bool;

    /**
     * Determine if has where part
     *
     * @return bool
     */
    public function hasWhere(): bool;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @return int
     */
    public function insertInto(RepositoryInterface $repository): int;

    /**
     * Determine if distinct result
     *
     * @return bool
     */
    public function isDistinctResult(): bool;

    /**
     * Determine if ID-fetch is enabled
     *
     * @return bool
     */
    public function isIdFetchEnabled(): bool;

    /**
     * Add join
     *
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string|number|array $right
     * @param string|null $alias
     * @return $this
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface;

    /**
     * Add join by where callback
     *
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string|null $alias
     * @return $this
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface;

    /**
     * Add left join
     *
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param $right
     * @param string|null $alias
     * @return $this
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface;

    /**
     * Add left join by where callback
     *
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string|null $alias
     * @return $this
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface;

    /**
     * Add right join
     *
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string|number|array $right
     * @param string|null $alias
     * @return $this
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface;

    /**
     * Add right join by where callback
     *
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string|null $alias
     * @return $this
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface;

    /**
     * Set alias
     *
     * @param string $alias
     * @return $this
     */
    public function setAlias(string $alias): RequestInterface;

    /**
     * Set columns
     *
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns): RequestInterface;

    /**
     * Set distinct result
     *
     * @param bool $distinctResult
     * @return $this
     */
    public function setDistinctResult(bool $distinctResult): RequestInterface;

    /**
     * Set group by parameters
     *
     * @param array $groupBy
     * @return $this
     */
    public function setGroupBy(array $groupBy): RequestInterface;

    /**
     * Set ID-fetch enabled
     *
     * @param bool $idFetchEnabled
     * @return $this
     */
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface;

    /**
     * Set limit
     *
     * @param int|null $limit
     * @return $this
     */
    public function setLimit(?int $limit): RequestInterface;

    /**
     * Set limit for page
     *
     * @param int $page
     * @param int $itemsPerPage
     * @return $this
     */
    public function setLimitForPage(int $page, int $itemsPerPage): RequestInterface;

    /**
     * Set offset
     *
     * @param int $offset
     * @return $this
     */
    public function setOffset(int $offset): RequestInterface;

    /**
     * Set order by parameters
     *
     * @param array $orderBy
     * @return $this
     */
    public function setOrderBy(array $orderBy): RequestInterface;

    /**
     * Set union limit
     *
     * @param int|null $unionLimit
     * @return $this
     */
    public function setUnionLimit(?int $unionLimit): RequestInterface;

    /**
     * Set union limit for page
     *
     * @param int $page
     * @param int $itemsPerPage
     * @return $this
     */
    public function setUnionLimitForPage(int $page, int $itemsPerPage): RequestInterface;

    /**
     * Set union offset
     *
     * @param int $unionOffset
     * @return $this
     */
    public function setUnionOffset(int $unionOffset): RequestInterface;

    /**
     * Set union order by parameters
     *
     * @param array $unionOrderBy
     * @return $this
     */
    public function setUnionOrderBy(array $unionOrderBy): RequestInterface;

    /**
     * Add union
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function union(RequestInterface $request): RequestInterface;

    /**
     * Add union all
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function unionAll(RequestInterface $request): RequestInterface;

    /**
     * Update
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;
}
