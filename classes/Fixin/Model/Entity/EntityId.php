<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

class EntityId extends Prototype implements EntityIdInterface {

    /**
     * @var array
     */
    protected $entityId;

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityIdInterface::deleteEntity()
     */
    public function deleteEntity(): EntityIdInterface {
        $this->getRepository()->where($this)->delete();

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityIdInterface::getEntity()
     */
    public function getEntity() {
        return $this->getRepository()->getEntityWithId($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityIdInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty('repository');
    }

    /**
     * Set entity ID
     *
     * @param array $entityId
     */
    protected function setEntityId(array $entityId) {
        $this->entityId = $entityId;
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