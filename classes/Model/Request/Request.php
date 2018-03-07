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
        JOIN_PROTOTYPE = '*\Model\Request\Join',
        THIS_SETS = [
            self::REPOSITORY => RepositoryInterface::class
        ],
        UNION_PROTOTYPE = '*\Model\Request\Union',
        WHERE_PROTOTYPE = '*\Model\Request\Where\Where';

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

    /**
     * @inheritDoc
     */
    public function __clone() {
        if ($this->having) {
            $this->having = clone $this->having;
        }

        if ($this->where) {
            $this->where = clone $this->where;
        }
    }

    /**
     * Add join
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param $right
     * @param string|null $alias
     */
    protected function addJoin(string $type, RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): void
    {
        $this->addJoinItem($type, $repository, $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class)->compare($left, $operator, $right, WhereInterface::TYPE_IDENTIFIER, WhereInterface::TYPE_IDENTIFIER), $alias);
    }

    /**
     * Add join by callback
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string|null $alias
     */
    protected function addJoinByCallback(string $type, RepositoryInterface $repository, callable $callback, string $alias = null): void
    {
        /** @var WhereInterface $where */
        $where = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class);
        $callback($where);

        $this->addJoinItem($type, $repository, $where, $alias);
    }

    /**
     * Add join item
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param WhereInterface|null $where
     * @param string|null $alias
     */
    protected function addJoinItem(string $type, RepositoryInterface $repository, WhereInterface $where = null, string $alias = null): void
    {
        $this->joins[] = $this->resourceManager->clone(static::JOIN_PROTOTYPE, JoinInterface::class, [
            JoinInterface::TYPE => $type,
            JoinInterface::REPOSITORY => $repository,
            JoinInterface::ALIAS => $alias ?? $repository->getName(),
            JoinInterface::WHERE => $where
        ]);
    }

    /**
     * Add union
     *
     * @param string $type
     * @param RequestInterface $request
     */
    protected function addUnion(string $type, RequestInterface $request): void
    {
        $this->unions[] = $this->resourceManager->clone(static::UNION_PROTOTYPE, UnionInterface::class, [
            UnionInterface::TYPE => $type,
            UnionInterface::REQUEST => $request
        ]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->fetchValue($this->createExpression(sprintf(static::COUNT_MASK, implode(',', $this->columns ?: ['*']))));
    }

    /**
     * @inheritDoc
     */
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->repository->createExpression($expression, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): RequestInterface
    {
        $this->addJoinItem(JoinInterface::TYPE_CROSS, $repository, null, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): int
    {
        return $this->repository->delete($this);
    }

    /**
     * @inheritDoc
     */
    public function fetch(): EntitySetInterface
    {
        return $this->repository->select($this);
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn($column): StorageResultInterface
    {
        return $this->repository->selectColumn((clone $this)->setColumns([$column]));
    }

    /**
     * @inheritDoc
     */
    public function fetchExistsValue(): bool
    {
        return $this->repository->selectExistsValue($this);
    }

    /**
     * @inheritDoc
     */
    public function fetchFirst(): ?EntityInterface
    {
        return (clone $this)
            ->setLimit(1)
            ->fetch()
            ->current();
    }

    /**
     * @inheritDoc
     */
    public function fetchRawData(): StorageResultInterface
    {
        return $this->repository->selectRawData($this);
    }

    /**
     * @inheritDoc
     */
    public function fetchValue($column)
    {
        return (clone $this)
            ->setLimit(1)
            ->fetchColumn($column)
            ->current();
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias ?? ($this->alias = $this->repository->getName());
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @inheritDoc
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @inheritDoc
     */
    public function getHaving(): WhereInterface
    {
        return $this->having ?? ($this->having = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class));
    }

    /**
     * @inheritDoc
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function getUnionLimit(): ?int
    {
        return $this->unionLimit;
    }

    /**
     * @inheritDoc
     */
    public function getUnionOffset(): int
    {
        return $this->unionOffset;
    }

    /**
     * @inheritDoc
     */
    public function getUnionOrderBy(): array
    {
        return $this->unionOrderBy;
    }

    /**
     * @inheritDoc
     */
    public function getUnions(): array
    {
        return $this->unions;
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): WhereInterface
    {
        return $this->where ?? ($this->where = $this->resourceManager->clone(static::WHERE_PROTOTYPE, WhereInterface::class));
    }

    /**
     * @inheritDoc
     */
    public function hasHaving(): bool
    {
        return isset($this->having);
    }

    /**
     * @inheritDoc
     */
    public function hasWhere(): bool
    {
        return isset($this->where);
    }

    /**
     * @inheritDoc
     */
    public function insertInto(RepositoryInterface $repository): int
    {
        return $this->repository->insertInto($repository, $this);
    }

    /**
     * @inheritDoc
     */
    public function isIdFetchEnabled(): bool
    {
        return $this->idFetchEnabled;
    }

    /**
     * @inheritDoc
     */
    public function isDistinctResult(): bool
    {
        return $this->distinctResult;
    }

    /**
     * @inheritDoc
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface
    {
        $this->addJoin(JoinInterface::TYPE_INNER, $repository, $left, $operator, $right, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface
    {
        $this->addJoinByCallback(JoinInterface::TYPE_INNER, $repository, $callback, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface
    {
        $this->addJoin(JoinInterface::TYPE_LEFT, $repository, $left, $operator, $right, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface
    {
        $this->addJoinByCallback(JoinInterface::TYPE_LEFT, $repository, $callback, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface
    {
        $this->addJoin(JoinInterface::TYPE_RIGHT, $repository, $left, $operator, $right, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface
    {
        $this->addJoinByCallback(JoinInterface::TYPE_RIGHT, $repository, $callback, $alias);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): RequestInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setColumns(array $columns): RequestInterface
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDistinctResult(bool $distinctResult): RequestInterface
    {
        $this->distinctResult = $distinctResult;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setGroupBy(array $groupBy): RequestInterface
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface
    {
        $this->idFetchEnabled = $idFetchEnabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLimit(?int $limit): RequestInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLimitForPage(int $page, int $itemsPerPage): RequestInterface
    {
        $this->offset = $page * $itemsPerPage;
        $this->limit = $itemsPerPage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): RequestInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOrderBy(array $orderBy): RequestInterface
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUnionLimit(?int $unionLimit): RequestInterface
    {
        $this->unionLimit = $unionLimit;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUnionLimitForPage(int $page, int $itemsPerPage): RequestInterface
    {
        $this->unionOffset = $page * $itemsPerPage;
        $this->unionLimit = $itemsPerPage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUnionOffset(int $unionOffset): RequestInterface
    {
        $this->unionOffset = $unionOffset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUnionOrderBy(array $unionOrderBy): RequestInterface
    {
        $this->unionOrderBy = $unionOrderBy;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function union(RequestInterface $request): RequestInterface
    {
        $this->addUnion(UnionInterface::TYPE_NORMAL, $request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unionAll(RequestInterface $request): RequestInterface
    {
        $this->addUnion(UnionInterface::TYPE_ALL, $request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(array $set): int
    {
        return $this->repository->update($set, $this);
    }
}
