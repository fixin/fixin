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
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression';

    /**
     * @var bool
     */
    protected $idFetchEnabled = true;

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
     * @see \Fixin\Model\Request\RequestInterface::update($set)
     */
    public function update(array $set): int {
        return $this->repository->update($set, $this);
    }
}