<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Prototype;

abstract class RequestBase extends Prototype implements RequestInterface {

    const
        PROTOTYPE_JOIN = 'Model\Request\Join',
        PROTOTYPE_WHERE = 'Model\Request\Where\Where',
        THIS_REQUIRES = [
            self::OPTION_REPOSITORY => self::TYPE_INSTANCE
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
     * @var Join[]
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
     * Add join
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param string $left
     * @param string $operator
     * @param string $right
     * @param string $alias
     * @return static
     */
    protected function addJoin(string $type, RepositoryInterface $repository, string $left, string $operator, $right, string $alias = null) {
        return $this->addJoinItem($type, $repository, $this->container->clonePrototype(static::PROTOTYPE_WHERE)->compare($left, $operator, $right, WhereInterface::TYPE_IDENTIFIER, WhereInterface::TYPE_IDENTIFIER), $alias);
    }

    /**
     * Add join intem
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param WhereInterface $where
     * @param string $alias
     * @return static
     */
    protected function addJoinItem(string $type, RepositoryInterface $repository, WhereInterface $where = null, string $alias = null) {
        $this->joins[] = $this->container->clonePrototype(static::PROTOTYPE_JOIN, [
            JoinInterface::OPTION_TYPE => $type,
            JoinInterface::OPTION_REPOSITORY => $repository,
            JoinInterface::OPTION_ALIAS => $alias ?? $repository->getName(),
            JoinInterface::OPTION_WHERE => $where
        ]);

        return $this;
    }

    /**
     * Add join by where callback
     *
     * @param string $type
     * @param RepositoryInterface $repository
     * @param callable $callback
     * @param string $alias
     * @return static
     */
    protected function addJoinWhere(string $type, RepositoryInterface $repository, callable $callback, string $alias = null) {
        $where = $this->container->clonePrototype(static::PROTOTYPE_WHERE);
        $callback($where);

        return $this->addJoinItem($type, $repository, $where, $alias);
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
}