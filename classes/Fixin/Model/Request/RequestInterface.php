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
        OPTION_REPOSITORY = 'repository';

    public function count(): int;
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface;

    /**
     * Add cross join
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): RequestInterface;

    public function delete(): int;
    public function fetch(): EntitySetInterface;

    /**
     * @param string|ExpressionInterface|RequestInterface $column
     */
    public function fetchColumn($column): StorageResultInterface;

    public function fetchExistsValue(): bool;
    public function fetchFirst() : ?EntityInterface;
    public function fetchRawData(): StorageResultInterface;

    /**
     * @param string|ExpressionInterface|RequestInterface $column
     */
    public function fetchValue($column);

    public function getAlias(): string;
    public function getColumns(): array;
    public function getGroupBy(): array;
    public function getHaving(): WhereInterface;

    /**
     * @return JoinInterface[]
     */
    public function getJoins(): array;

    public function getLimit(): ?int;
    public function getOffset(): int;
    public function getOrderBy(): array;
    public function getRepository(): RepositoryInterface;
    public function getUnionLimit(): ?int;
    public function getUnionOffset(): int;
    public function getUnionOrderBy(): array;

    /**
     * @return UnionInterface[]
     */
    public function getUnions(): array;

    public function getWhere(): WhereInterface;
    public function hasHaving(): bool;
    public function hasWhere(): bool;
    public function insertInto(RepositoryInterface $repository): int;
    public function isDistinctResult(): bool;
    public function isIdFetchEnabled(): bool;

    /**
     * Add join
     *
     * @param string|number|array $right
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface;

    /**
     * Add join by where callback
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface;

    /**
     * Add left join
     *
     * @param string|number|array $right
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface;

    /**
     * Add left join by where callback
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface;

    /**
     * Add right join
     *
     * @param string|number|array $right
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface;

    /**
     * Add right join by where callback
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface;

    public function setAlias(string $alias): RequestInterface;
    public function setColumns(array $columns): RequestInterface;
    public function setDistinctResult(bool $distinctResult): RequestInterface;
    public function setGroupBy(array $groupBy): RequestInterface;
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface;
    public function setLimit(?int $limit): RequestInterface;
    public function setLimitForPage(int $page, int $itemsPerPage): RequestInterface;
    public function setOffset(int $offset): RequestInterface;
    public function setOrderBy(array $orderBy): RequestInterface;
    public function setUnionLimit(?int $unionLimit): RequestInterface;
    public function setUnionLimitForPage(int $page, int $itemsPerPage): RequestInterface;
    public function setUnionOffset(int $unionOffset): RequestInterface;
    public function setUnionOrderBy(array $unionOrderBy): RequestInterface;

    /**
     * Add union
     */
    public function union(RequestInterface $request): RequestInterface;

    /**
     * Add union all
     */
    public function unionAll(RequestInterface $request): RequestInterface;
    public function update(array $set): int;
}
