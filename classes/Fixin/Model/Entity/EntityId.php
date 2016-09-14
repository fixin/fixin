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

    const
        SEPARATOR = ',',
        THIS_REQUIRES = [
            self::OPTION_REPOSITORY => self::TYPE_INSTANCE
        ],
        THIS_SETS_LAZY = [
            self::OPTION_REPOSITORY => RepositoryInterface::class
        ]
    ;

    /**
     * @var array
     */
    protected $entityId = [];

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @var string
     */
    protected $string;

    public function __toString() {
        return $this->string;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityIdInterface::deleteEntity()
     */
    public function deleteEntity(): bool {
        $request = $this->getRepository()->createRequest();
        $request->getWhere()->items($this->entityId);

        return $request->delete() > 0;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityIdInterface::getEntity()
     */
    public function getEntity() {
        return $this->getRepository()->selectEntityById($this);
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
        $this->string = implode(static::SEPARATOR, $entityId);
    }
}