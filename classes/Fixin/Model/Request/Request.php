<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
     * @var Union[]
     */
    protected $unions = [];

    protected function addUnion(string $type, RequestInterface $request): void
    {
        $this->unions[] = $this->container->clonePrototype(static::PROTOTYPE_UNION, [
            UnionInterface::OPTION_TYPE => $type,
            UnionInterface::OPTION_REQUEST => $request
        ]);
    }

    public function count(): int
    {
        return $this->fetchValue($this->createExpression(sprintf(static::MASK_COUNT, implode(',', $this->columns ?: ['*']))));
    }

    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->container->clonePrototype(static::PROTOTYPE_EXPRESSION, [
            ExpressionInterface::OPTION_EXPRESSION => $expression,
            ExpressionInterface::OPTION_PARAMETERS => $parameters
        ]);
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
        return $this->repository->selectExists($this);
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
        return $this->fetchColumn($column)->current();
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
     * @return static
     */
    public function setIdFetchEnabled(bool $idFetchEnabled): RequestInterface
    {
        $this->idFetchEnabled = $idFetchEnabled;

        return $this;
    }

    /**
     * @return static
     */
    public function setUnionLimit(?int $unionLimit): RequestInterface
    {
        $this->unionLimit = $unionLimit;

        return $this;
    }

    /**
     * @return static
     */
    public function setUnionLimitForPage(int $page, int $itemsPerPage): RequestInterface
    {
        $this->unionOffset = $page * $itemsPerPage;
        $this->unionLimit = $itemsPerPage;

        return $this;
    }

    /**
     * @return static
     */
    public function setUnionOffset(int $unionOffset): RequestInterface
    {
        $this->unionOffset = $unionOffset;

        return $this;
    }

    /**
     * @return static
     */
    public function setUnionOrderBy(array $unionOrderBy): RequestInterface
    {
        $this->unionOrderBy = $unionOrderBy;

        return $this;
    }

    /**
     * @return static
     */
    public function union(RequestInterface $request): RequestInterface
    {
        $this->addUnion(UnionInterface::TYPE_NORMAL, $request);

        return $this;
    }

    /**
     * @return static
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
