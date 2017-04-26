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
use Fixin\Resource\Prototype;
use Fixin\Support\DebugDescriptionTrait;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Request extends Prototype implements RequestInterface
{
    use DebugDescriptionTrait;

    protected const
        COUNT_MASK = 'COUNT(%s)',
        JOIN_PROTOTYPE = 'Model\Request\Join',
        THIS_SETS = [
            self::REPOSITORY => RepositoryInterface::class
        ],
        UNION_PROTOTYPE = 'Model\Request\Union',
        WHERE_PROTOTYPE = 'Model\Request\Where\Where';

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected $distinctResult = false;

    /**
     * @var array
     */
    protected $groupBy = [];

    /**
     * @var WhereInterface|null
     */
    protected $having;

    /**
     * @var bool
     */
    protected $idFetchEnabled = true;

    /**
     * @var JoinInterface[]
     */
    protected $joins = [];

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var integer
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var int|null
     */
    protected $unionLimit;

    /**
     * @var integer
     */
    protected $unionOffset = 0;

    /**
     * @var array
     */
    protected $unionOrderBy = [];

    /**
     * @var Union[]
     */
    protected $unions = [];

    /**
     * @var WhereInterface|null
     */
    protected $where;

    public function __clone() {
        if ($this->having) {
            $this->having = clone $this->having;
        }

        if ($this->where) {
            $this->where = clone $this->where;
        }
    }

    protected function addJoin(string $type, RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): void
    {
        $this->addJoinItem($type, $repository, $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class)->compare($left, $operator, $right, WhereInterface::TYPE_IDENTIFIER, WhereInterface::TYPE_IDENTIFIER), $alias);
    }

    protected function addJoinItem(string $type, RepositoryInterface $repository, WhereInterface $where = null, string $alias = null): void
    {
        $this->joins[] = $this->resourceManager->clone(static::JOIN_PROTOTYPE, JoinInterface::class, [
            JoinInterface::TYPE => $type,
            JoinInterface::REPOSITORY => $repository,
            JoinInterface::ALIAS => $alias ?? $repository->getName(),
            JoinInterface::WHERE => $where
        ]);
    }

    protected function addJoinWhere(string $type, RepositoryInterface $repository, callable $callback, string $alias = null): void
    {
        /** @var WhereInterface $where */
        $where = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class);
        $callback($where);

        $this->addJoinItem($type, $repository, $where, $alias);
    }

    protected function addUnion(string $type, RequestInterface $request): void
    {
        $this->unions[] = $this->resourceManager->clone(static::UNION_PROTOTYPE, UnionInterface::class, [
            UnionInterface::TYPE => $type,
            UnionInterface::REQUEST => $request
        ]);
    }

    public function count(): int
    {
        return $this->fetchValue($this->createExpression(sprintf(static::COUNT_MASK, implode(',', $this->columns ?: ['*']))));
    }

    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->repository->createExpression($expression, $parameters);
    }

    /**
     * @return $this
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): RequestInterface
    {
        $this->addJoinItem(JoinInterface::TYPE_CROSS, $repository, null, $alias);

        return $this;
    }

    public function delete(): int
    {
        return $this->repository->delete($this);
    }

    public function fetch(): EntitySetInterface
    {
        return $this->repository->select($this);
    }

    public function fetchColumn($column): StorageResultInterface
    {
        return $this->repository->selectColumn((clone $this)->setColumns([$column]));
    }

    public function fetchExistsValue(): bool
    {
        return $this->repository->selectExistsValue($this);
    }

    public function fetchFirst(): ?EntityInterface
    {
        return (clone $this)
            ->setLimit(1)
            ->fetch()
            ->current();
    }

    public function fetchRawData(): StorageResultInterface
    {
        return $this->repository->selectRawData($this);
    }

    public function fetchValue($column)
    {
        return (clone $this)
            ->setLimit(1)
            ->fetchColumn($column)
            ->current();
    }

    public function getAlias(): string
    {
        return $this->alias ?? ($this->alias = $this->repository->getName());
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function getHaving(): WhereInterface
    {
        return $this->having ?? ($this->having = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class));
    }

    public function getJoins(): array
    {
        return $this->joins;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getUnionLimit(): ?int
    {
        return $this->unionLimit;
    }

    public function getUnionOffset(): int
    {
        return $this->unionOffset;
    }

    public function getUnionOrderBy(): array
    {
        return $this->unionOrderBy;
    }

    public function getUnions(): array
    {
        return $this->unions;
    }

    public function getWhere(): WhereInterface
    {
        return $this->where ?? ($this->where = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class));
    }

    public function hasHaving(): bool
    {
        return isset($this->having);
    }

    public function hasWhere(): bool
    {
        return isset($this->where);
    }

    public function insertInto(RepositoryInterface $repository): int
    {
        return $this->repository->insertInto($repository, $this);
    }

    public function isIdFetchEnabled(): bool
    {
        return $this->idFetchEnabled;
    }

    public function isDistinctResult(): bool
    {
        return $this->distinctResult;
    }

    /**
     * @param array|number|string $right
     * @return $this
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface
    {
        $this->addJoin(JoinInterface::TYPE_INNER, $repository, $left, $operator, $right, $alias);

        return $this;
    }

    /**
     * @return $this
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface
    {
        $this->addJoinWhere(JoinInterface::TYPE_INNER, $repository, $callback, $alias);

        return $this;
    }

    /**
     * @param array|number|string $right
     * @return $this
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface
    {
        $this->addJoin(JoinInterface::TYPE_LEFT, $repository, $left, $operator, $right, $alias);

        return $this;
    }

    /**
     * @return $this
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface
    {
        $this->addJoinWhere(JoinInterface::TYPE_LEFT, $repository, $callback, $alias);

        return $this;
    }

    /**
     * @param array|number|string $right
     * @return $this
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface
    {
        $this->addJoin(JoinInterface::TYPE_RIGHT, $repository, $left, $operator, $right, $alias);

        return $this;
    }

    /**
     * @return $this
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface
    {
        $this->addJoinWhere(JoinInterface::TYPE_RIGHT, $repository, $callback, $alias);

        return $this;
    }

    /**
     * @return $this
     */
    public function setAlias(string $alias): RequestInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return $this
     */
    public function setColumns(array $columns): RequestInterface
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDistinctResult(bool $distinctResult): RequestInterface
    {
        $this->distinctResult = $distinctResult;

        return $this;
    }

    /**
     * @return $this
     */
    public function setGroupBy(array $groupBy): RequestInterface
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * @return $this
     */
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface
    {
        $this->idFetchEnabled = $idFetchEnabled;

        return $this;
    }

    /**
     * @return $this
     */
    public function setLimit(?int $limit): RequestInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return $this
     */
    public function setLimitForPage(int $page, int $itemsPerPage): RequestInterface
    {
        $this->offset = $page * $itemsPerPage;
        $this->limit = $itemsPerPage;

        return $this;
    }

    /**
     * @return $this
     */
    public function setOffset(int $offset): RequestInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return $this
     */
    public function setOrderBy(array $orderBy): RequestInterface
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return $this
     */
    public function setUnionLimit(?int $unionLimit): RequestInterface
    {
        $this->unionLimit = $unionLimit;

        return $this;
    }

    /**
     * @return $this
     */
    public function setUnionLimitForPage(int $page, int $itemsPerPage): RequestInterface
    {
        $this->unionOffset = $page * $itemsPerPage;
        $this->unionLimit = $itemsPerPage;

        return $this;
    }

    /**
     * @return $this
     */
    public function setUnionOffset(int $unionOffset): RequestInterface
    {
        $this->unionOffset = $unionOffset;

        return $this;
    }

    /**
     * @return $this
     */
    public function setUnionOrderBy(array $unionOrderBy): RequestInterface
    {
        $this->unionOrderBy = $unionOrderBy;

        return $this;
    }

    /**
     * @return $this
     */
    public function union(RequestInterface $request): RequestInterface
    {
        $this->addUnion(UnionInterface::TYPE_NORMAL, $request);

        return $this;
    }

    /**
     * @return $this
     */
    public function unionAll(RequestInterface $request): RequestInterface
    {
        $this->addUnion(UnionInterface::TYPE_ALL, $request);

        return $this;
    }

    public function update(array $set): int
    {
        return $this->repository->update($set, $this);
    }
}
