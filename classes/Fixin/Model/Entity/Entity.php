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

abstract class Entity extends Prototype implements EntityInterface {

    const EXCEPTION_NOT_STORED_ENTITY = 'Not stored entity';
    const THIS_SETS_LAZY = [
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var bool
     */
    protected $deleted = false;

    /**
     * @var EntityIdInterface
     */
    protected $entityId;

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @return array
     */
    abstract protected function collectSaveData(): array;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::delete()
     */
    public function delete(): EntityInterface {
        if ($this->isStored()) {
            $this->deleted = $this->entityId->deleteEntity();
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
     * @see \Fixin\Model\Entity\EntityInterface::isDeleted()
     */
    public function isDeleted(): bool {
        return $this->deleted;
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
     * @see \Fixin\Model\Entity\EntityInterface::save()
     */
    public function save(): EntityInterface {
        if ($this->entityId) {
            $request = $this->getRepositry()->createRequest();
            $request->where()->todo($this->entityId);
            $request->update($this->collectSaveData());
            $this->deleted = false;

            return $this;
        }

        $this->entityId = $this->getRepository()->insert($this->collectSaveData());
        $this->deleted = false;

        return $this;
    }
}