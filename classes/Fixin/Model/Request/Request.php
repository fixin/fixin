<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Storage\StorageResultInterface;

class Request extends RequestBase {

    const
        MASK_COUNT = 'COUNT(%s)',
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression',
        PROTOTYPE_UNION = 'Model\Request\Union';

    /**
     * @var bool
     */
    protected $idFetchEnabled = true;

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
     * @var array
     */
    protected $unions = [];

    /**
     * Add union
     *
     * @param string $type
     * @param RequestInterface $request
     * @return self
     */
    protected function addUnion(string $type, RequestInterface $request) {
        $this->unions[] = $this->container->clonePrototype(static::PROTOTYPE_UNION, [
            UnionInterface::OPTION_TYPE => $type,
            UnionInterface::OPTION_REQUEST => $request
        ]);

        return $this;
    }

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
     * @see \Fixin\Model\Request\RequestInterface::insertInto($repository)
     */
    public function insertInto(RepositoryInterface $repository): int {
        return $this->repository->insertInto($repository, $this);
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
     * @see \Fixin\Model\Request\RequestInterface::setIdFetchEnabled($idFetchEnabled)
     */
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface {
        $this->idFetchEnabled = $idFetchEnabled;

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