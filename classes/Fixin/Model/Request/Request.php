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

class Request extends RequestBase {

    const
        MASK_COUNT = 'COUNT(%s)',
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression';

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
     * @var bool
     */
    protected $idFetchEnabled = true;

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
     * @var WhereInterface
     */
    protected $where;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::count()
     */
    public function count(): int {
        return $this->fetchValue($this->createExpression(sprintf(static::MASK_COUNT, implode(',', $this->columns ?: ['*']))));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::createExpression($expression, $parameters)
     */
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface {
        return $this->container->clonePrototype(static::PROTOTYPE_EXPRESSION, [
            ExpressionInterface::OPTION_EXPRESSION => $expression,
            ExpressionInterface::OPTION_PARAMETERS => $parameters
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::crossJoin($repository, $alias)
     */
    public function crossJoin(RepositoryInterface $repository, string $alias = null): RequestInterface {
        return $this->addJoinItem(JoinInterface::TYPE_CROSS, $repository, null, $alias);
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
        return $this->repository->exists($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetch()
     */
    public function fetch(): EntitySetInterface {
        return $this->repository->select($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetchColumn($column)
     */
    public function fetchColumn($column): StorageResultInterface {
        return $this->repository->selectColumn((clone $this)->setColumns([$column]));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::fetchFirst()
     */
    public function fetchFirst() {
        return (clone $this)->setLimit(1)->fetch()->current();
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
        return $this->fetchColumn($column)->current();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getAlias()
     */
    public function getAlias(): string {
        return $this->alias ?? ($this->alias = $this->repository->getName());
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
        return $this->having ?? ($this->having = $this->container->clonePrototype(static::PROTOTYPE_WHERE));
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
     * @see \Fixin\Model\Request\RequestInterface::getUnionLimit()
     */
    public function getUnionLimit() {
        return $this->unionLimit;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getUnionOffset()
     */
    public function getUnionOffset(): int {
        return $this->unionOffset;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getUnionOrderBy()
     */
    public function getUnionOrderBy(): array {
        return $this->unionOrderBy;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getUnions()
     */
    public function getUnions(): array {
        return $this->unions;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::getWhere()
     */
    public function getWhere(): WhereInterface {
        return $this->where ?? ($this->where = $this->container->clonePrototype(static::PROTOTYPE_WHERE));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::hasHaving()
     */
    public function hasHaving(): bool {
        return isset($this->having);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::hasWhere()
     */
    public function hasWhere(): bool {
        return isset($this->where);
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
     * @see \Fixin\Model\Request\RequestInterface::isIdFetchEnabled()
     */
    public function isIdFetchEnabled(): bool {
        return $this->idFetchEnabled;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::join($repository, $left, $operator, $right, $alias)
     */
    public function join(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface {
        return $this->addJoin(JoinInterface::TYPE_INNER, $repository, $left, $operator, $right, $alias);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::joinWhere($repository, $callback, $alias)
     */
    public function joinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface {
        return $this->addJoinWhere(JoinInterface::TYPE_INNER, $repository, $callback, $alias);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::leftJoin($repository, $left, $operator, $right, $alias)
     */
    public function leftJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface {
        return $this->addJoin(JoinInterface::TYPE_LEFT, $repository, $left, $operator, $right, $alias);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::leftJoinWhere($repository, $callback, $alias)
     */
    public function leftJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface {
        return $this->addJoinWhere(JoinInterface::TYPE_LEFT, $repository, $callback, $alias);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::rightJoin($repository, $left, $operator, $right, $alias)
     */
    public function rightJoin(RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null): RequestInterface {
        return $this->addJoin(JoinInterface::TYPE_RIGHT, $repository, $left, $operator, $right, $alias);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::rightJoinWhere($repository, $callback, $alias)
     */
    public function rightJoinWhere(RepositoryInterface $repository, callable $callback, string $alias = null): RequestInterface {
        return $this->addJoinWhere(JoinInterface::TYPE_RIGHT, $repository, $callback, $alias);
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
     * @see \Fixin\Model\Request\RequestInterface::setIdFetchEnabled($idFetchEnabled)
     */
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface {
        $this->idFetchEnabled = $idFetchEnabled;

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
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setUnionLimit($unionLimit)
     */
    public function setUnionLimit($unionLimit): RequestInterface {
        $this->unionLimit = $unionLimit;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setUnionLimitForPage($page, $itemsPerPage)
     */
    public function setUnionLimitForPage(int $page, int $itemsPerPage): RequestInterface {
        $this->unionOffset = $page * $itemsPerPage;
        $this->unionLimit = $itemsPerPage;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setUnionOffset($unionOffset)
     */
    public function setUnionOffset(int $unionOffset): RequestInterface {
        $this->unionOffset = $unionOffset;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::setUnionOrderBy($unionOrderBy)
     */
    public function setUnionOrderBy(array $unionOrderBy): RequestInterface {
        $this->unionOrderBy = $unionOrderBy;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::union($request)
     */
    public function union(RequestInterface $request): RequestInterface {
        return $this->addUnion(UnionInterface::TYPE_NORMAL, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::unionAll($request)
     */
    public function unionAll(RequestInterface $request): RequestInterface {
        return $this->addUnion(UnionInterface::TYPE_ALL, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\RequestInterface::update($set)
     */
    public function update(array $set): int {
        return $this->repository->update($set, $this);
    }
}