<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

class Entity extends Prototype implements EntityInterface {

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::delete()
     */
    public function delete(): self {
        $this->getRepository()->delete($this);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty('repository');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::save()
     */
    public function save(): self {
        $this->getRepository()->save($this);

        return $this;
    }

    /**
     * Set repository
     *
     * @param string|RepositoryInterface $repository
     */
    protected function setRepository($repository) {
        $this->setLazyLoadingProperty('repository', RepositoryInterface::class, $repository);
    }
}