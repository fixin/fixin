<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Prototype;

abstract class RequestBase extends Prototype implements RequestInterface
{
    protected const
        JOIN_PROTOTYPE = 'Model\Request\Join',
        WHERE_PROTOTYPE = 'Model\Request\Where\Where',
        THIS_REQUIRES = [
            self::REPOSITORY
        ],
        THIS_SETS = [
            self::REPOSITORY => RepositoryInterface::class
        ];

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
     * @var WhereInterface|null
     */
    protected $where;

    public function __clone() {
        $this->having = clone $this->having;
        $this->where = clone $this->where;
    }

    protected function addJoin(string $type, RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): void
    {
        $this->addJoinItem($type, $repository, $this->resourceManager->clone(static::WHERE_PROTOTYPE)->compare($left, $operator, $right, WhereInterface::TYPE_IDENTIFIER, WhereInterface::TYPE_IDENTIFIER), $alias);
    }

    protected function addJoinItem(string $type, RepositoryInterface $repository, WhereInterface $where = null, string $alias = null): void
    {
        $this->joins[] = $this->resourceManager->clone(static::JOIN_PROTOTYPE, [
            JoinInterface::TYPE => $type,
            JoinInterface::REPOSITORY => $repository,
            JoinInterface::ALIAS => $alias ?? $repository->getName(),
            JoinInterface::WHERE => $where
        ]);
    }

    protected function addJoinWhere(string $type, RepositoryInterface $repository, callable $callback, string $alias = null): void
    {
        /** @var WhereInterface $where */
        $where = $this->resourceManager->clone(static::WHERE_PROTOTYPE);
        $callback($where);

        $this->addJoinItem($type, $repository, $where, $alias);
    }

    /**
     * @return $this
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): RequestInterface
    {
        $this->addJoinItem(JoinInterface::TYPE_CROSS, $repository, null, $alias);

        return $this;
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
        return $this->having ?? ($this->having = $this->resourceManager->clone(static::WHERE_PROTOTYPE));
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

    public function getWhere(): WhereInterface
    {
        return $this->where ?? ($this->where = $this->resourceManager->clone(static::WHERE_PROTOTYPE));
    }

    public function hasHaving(): bool
    {
        return isset($this->having);
    }

    public function hasWhere(): bool
    {
        return isset($this->where);
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
}
