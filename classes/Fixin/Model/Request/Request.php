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
use Fixin\Model\Storage\StorageResultInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Request extends RequestBase {

    protected const
        COUNT_MASK = 'COUNT(%s)',
        UNION_PROTOTYPE = 'Model\Request\Union';

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
     * @var Union[]
     */
    protected $unions = [];

    protected function addUnion(string $type, RequestInterface $request): void
    {
        $this->unions[] = $this->resourceManager->clone(static::UNION_PROTOTYPE, [
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

    public function insertInto(RepositoryInterface $repository): int
    {
        return $this->repository->insertInto($repository, $this);
    }

    public function isIdFetchEnabled(): bool
    {
        return $this->idFetchEnabled;
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
