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