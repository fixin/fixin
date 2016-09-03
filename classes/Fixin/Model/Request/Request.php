<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Prototype;

class Request extends Prototype implements RequestInterface {

    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];
    const WHERE_PROTOTYPE = 'Model\Request\Where\Where';

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
    protected $disctinctResult = false;

    /**
     * @var array
     */
    protected $groupBy = [];

    /**
     * @var WhereInterface
     */
    protected $having;

    /**
     * @var array
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
     * @var WhereInterface
     */
    protected $where;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::count()
     */
    public function count(): int {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::crossJoin($repository)
     */
    public function crossJoin(RepositoryInterface $repository): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::delete()
     */
    public function delete(): int {
        return $this->repository->delete($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::exists()
     */
    public function exists(): bool {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetch()
     */
    public function fetch(): EntitySetInterface {
        return $this->repository->selectEntities($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetchColumn($column)
     */
    public function fetchColumn(string $column): array {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetchFirst()
     */
    public function fetchFirst() {
        $copy = clone $this;

        return $copy->setLimit(1)->fetch()->current();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetchRawData()
     */
    public function fetchRawData(): StorageResultInterface {
        return $this->repository->selectRawData($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetchValue($column)
     */
    public function fetchValue($column) {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getAlias()
     */
    public function getAlias(): string {
        return $this->alias;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getColumns()
     */
    public function getColumns(): array {
        return $this->columns;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getGroupBy()
     */
    public function getGroupBy(): array {
        return $this->groupBy;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getHaving()
     */
    public function getHaving(): WhereInterface {
        return $this->having ?? ($this->having = $this->container->clonePrototype(static::WHERE_PROTOTYPE));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getJoins()
     */
    public function getJoins(): array {
        return $this->joins;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getLimit()
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getOffset()
     */
    public function getOffset(): int {
        return $this->offset;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getOrderBy()
     */
    public function getOrderBy(): array {
        return $this->orderBy;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getWhere()
     */
    public function getWhere(): WhereInterface {
        return $this->where ?? ($this->where = $this->container->clonePrototype(static::WHERE_PROTOTYPE));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::insertInto($repository)
     */
    public function insertInto(RepositoryInterface $repository): int {
        return $this->repository->insertInto($repository, $this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::isDistinctResult()
     */
    public function isDistinctResult(): bool {
        return $this->disctinctResult;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::join($repository, $left, $operator, $right, $alias)
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::joinWhere($repository, $callback, $alias)
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::leftJoin($repository, $left, $operator, $right, $alias)
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::leftJoinWhere($repository, $callback, $alias)
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::rightJoin($repository, $left, $operator, $right, $alias)
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::rightJoinWhere($repository, $callback, $alias)
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setAlias($alias)
     */
    public function setAlias(string $alias): RequestInterface {
        $this->alias = $alias;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setColumns($columns)
     */
    public function setColumns(array $columns): RequestInterface {
        $this->columns = $columns;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setDistinctResult($disctinctResult)
     */
    public function setDistinctResult(bool $disctinctResult): RequestInterface {
        $this->disctinctResult = $disctinctResult;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setGroupBy($groupBy)
     */
    public function setGroupBy(array $groupBy): RequestInterface {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setLimit($limit)
     */
    public function setLimit($limit): RequestInterface {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setLimitForPage($page, $itemsPerPage)
     */
    public function setLimitForPage(int $page, int $itemsPerPage): RequestInterface {
        $this->offset = $page * $itemsPerPage;
        $this->limit = $itemsPerPage;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setOffset($offset)
     */
    public function setOffset(int $offset): RequestInterface {
        $this->offset = $offset;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setOrderBy($orderBy)
     */
    public function setOrderBy(array $orderBy): RequestInterface {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Set repository
     *
     * @param RepositoryInterface $repository
     */
    protected function setRepository(RepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::union($request)
     */
    public function union(RequestInterface $request): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::unionAll($request)
     */
    public function unionAll(RequestInterface $request): RequestInterface {
        // TODO
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::update($set)
     */
    public function update(array $set): int {
        return $this->repository->update($set, $this);
    }
}