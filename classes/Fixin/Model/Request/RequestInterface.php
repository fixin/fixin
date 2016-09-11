<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface RequestInterface extends PrototypeInterface {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Count items
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
     * @param string $alias
     * @return self
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): self;

    /**
     * Delete
     *
     * @return int
     */
    public function delete(): int;

    /**
     * Check items existance
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Fetch entities
     *
     * @return EntitySetInterface
     */
    public function fetch(): EntitySetInterface;

    /**
     * Fetch column values
     *
     * @param string|ExpressionInterface|RequestInterface $column
     * @return StorageResultInterface
     */
    public function fetchColumn($column): StorageResultInterface;

    /**
     * Fetch first entity
     *
     * @return EntityInterface|null
     */
    public function fetchFirst();

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
     * @return mixed|null
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
     * Get group by
     *
     * @return array
     */
    public function getGroupBy(): array;

    /**
     * Get having
     *
     * @return WhereInterface
     */
    public function getHaving(): WhereInterface;

    /**
     * Get joins
     *
     * @return array
     */
    public function getJoins(): array;

    /**
     * Get limit
     *
     * @return int|null
     */
    public function getLimit();

    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Get order by
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
    public function getUnionLimit();

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
     * Get unions
     *
     * @return array
     */
    public function getUnions(): array;

    /**
     * Get where
     *
     * @return WhereInterface
     */
    public function getWhere(): WhereInterface;

    /**
     * Has having
     *
     * @return bool
     */
    public function hasHaving(): bool;

    /**
     * Has where
     *
     * @return bool
     */
    public function hasWhere(): bool;

    /**
     * Insert into repository
     *
     * @param RepositoryInterface $repository
     * @return int
     */
    public function insertInto(RepositoryInterface $repository): int;

    /**
     * Is result distinct
     *
     * @return bool
     */
    public function isDistinctResult(): bool;

    /**
     * Add join
     *
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string|number|array $right
     * @param string $alias
     * @return self
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): self;

    /**
     * Add join by where callback
     *
     * @param RepositoryInterface $repository
     * @param callback $callback
     * @param string $alias
     * @return self
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): self;

    /**
     * Add left join
     *
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string|number|array $right
     * @param string $alias
     * @return self
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): self;

    /**
     * Add left join by where callback
     *
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string $alias
     * @return self
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): self;

    /**
     * Add right join
     *
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string|number|array $right
     * @param string $alias
     * @return self
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): self;

    /**
     * Add right join by where callback
     *
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string $alias
     * @return self
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): self;

    /**
     * Set alias
     *
     * @param string $alias
     * @return self
     */
    public function setAlias(string $alias): self;

    /**
     * Set columns
     *
     * @param array $columns
     * @return self
     */
    public function setColumns(array $columns): self;

    /**
     * Set distinct result
     *
     * @param bool $disctinctResult
     * @return self
     */
    public function setDistinctResult(bool $disctinctResult): self;

    /**
     * Set group by
     *
     * @param array $groupBy
     * @return self
     */
    public function setGroupBy(array $groupBy): self;

    /**
     * Set limit
     *
     * @param int|null $limit
     * @return self
     */
    public function setLimit($limit): self;

    /**
     * Set limit for page
     *
     * @param int $page
     * @param int $itemsPerPage
     * @return self
     */
    public function setLimitForPage(int $page, int $itemsPerPage): self;

    /**
     * Set offset
     *
     * @param int $offset
     * @return self
     */
    public function setOffset(int $offset): self;

    /**
     * Set order by
     *
     * @param array $orderBy
     * @return self
     */
    public function setOrderBy(array $orderBy): self;

    /**
     * Set union limit
     *
     * @param int|null $unionLimit
     * @return self
     */
    public function setUnionLimit($unionLimit): self;

    /**
     * Set union limit for page
     *
     * @param int $page
     * @param int $itemsPerPage
     * @return self
     */
    public function setUnionLimitForPage(int $page, int $itemsPerPage): self;

    /**
     * Set union offset
     *
     * @param int $unionOffset
     * @return self
     */
    public function setUnionOffset(int $unionOffset): self;

    /**
     * Set order by
     *
     * @param array $unionOrderBy
     * @return self
     */
    public function setUnionOrderBy(array $unionOrderBy): self;

    /**
     * Add a union
     *
     * @param RequestInterface $request
     * @return self
     */
    public function union(RequestInterface $request): self;

    /**
     * Add a union all
     *
     * @param RequestInterface $request
     * @return self
     */
    public function unionAll(RequestInterface $request): self;

    /**
     * Update
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;
}