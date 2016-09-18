<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Entity\Exception\NotStoredEntityException;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\ToStringTrait;

abstract class Entity extends Prototype implements EntityInterface {

    use ToStringTrait;

    const
    EXCEPTION_NOT_STORED_ENTITY = 'Not stored entity',
    THIS_SETS_LAZY = [
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var array
     */
    protected $deletableRelatedEntities = array();

    /**
     * @var EntityIdInterface
     */
    protected $entityId;

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::delete()
     */
    public function delete(): EntityInterface {
        if ($this->isStored()) {
            $this->entityId->deleteEntity();
            $this->entityId = null;

            return $this;
        }

        throw new NotStoredEntityException(static::EXCEPTION_NOT_STORED_ENTITY);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::getEntityId()
     */
    public function getEntityId() {
        return $this->entityId;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::getRepository()
     */
    public function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::isStored()
     */
    public function isStored(): bool {
        return isset($this->entityId);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::refresh()
     */
    public function refresh(): EntityInterface {
        $this->getRepository()->refresh($this);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::save()
     */
    public function save(): EntityInterface {
        $this->entityId = $this->getRepository()->save($this);
        $this->saveRelated();

        return $this;
    }

    /**
     * Save related entities
     *
     * @return \Fixin\Model\Entity\Entity
     */
    protected function saveRelated() {
        foreach ($this->deletableRelatedEntities as $entity) {
            $entity->delete();
        }

        $this->deletableRelatedEntities = [];

        return $this;
    }

    /**
     * Set entity id
     *
     * @param EntityIdInterface $entityId
     */
    protected function setEntityId(EntityIdInterface $entityId) {
        $this->entityId = $entityId;
    }
}