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

    const THIS_REQUIRES = [
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var array
     */
    protected $entityId = [];

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
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    /**
     * Set entity ID
     *
     * @param array $entityId
     */
    protected function setEntityId(array $entityId) {
        $this->entityId = $entityId;
    }
}