<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Resource\Prototype;

class RepositoryRequest extends Prototype implements RepositoryRequestInterface {

    const THIS_REQUIRES = [
        static::OPTION_REPOSITORY => static::TYPE_INSTANCE
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
     * @see \Fixin\Model\Repository\RepositoryRequestInterface::get()
     */
    public function get(): EntitySetInterface {
        return $this->repository->get($this);
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
}