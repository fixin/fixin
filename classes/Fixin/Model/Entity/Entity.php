<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Exception\RuntimeException;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

class Entity extends Prototype implements EntityInterface {

    const EXCEPTION_NO_ENTITY_ID = 'No entity ID';
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
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::delete()
     */
    public function delete(): EntityInterface {
        if (is_null($this->entityId)) {
            throw new RuntimeException(static::EXCEPTION_NO_ENTITY_ID);
        }

        $this->deleted = $this->entityId->deleteEntity();

        return $this;
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
     * @see \Fixin\Model\Entity\EntityInterface::isCreated()
     */
    public function isCreated(): bool {
        return is_null($this->entityId);
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
     * @see \Fixin\Model\Entity\EntityInterface::save()
     */
    public function save(): EntityInterface {
// TODO         $this->getRepository()->where($this->entityId)->update($set);

        return $this;
    }
}