<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Resource\Prototype;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Storage\StorageResultInterface;

class RepositoryRequest extends Prototype implements RepositoryRequestInterface {

    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::delete()
     */
    public function delete(): int {
        return $this->repository->delete($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::first()
     */
    public function first() {
        $copy = clone $this;

        return $copy->limit(1)->get()->current();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::get()
     */
    public function get(): EntitySetInterface {
        return $this->repository->selectEntities($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::getRawData()
     */
    public function getRawData(): StorageResultInterface {
        return $this->repository->selectRawData($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::limit($limit)
     */
    public function limit(int $limit): RepositoryRequestInterface {
        // TODO

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
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::update($set)
     */
    public function update(array $set): int {
        return $this->repository->update($set, $this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::where($where)
     */
    public function where($where): RepositoryRequestInterface {
        // TODO

        return $this;
    }
}