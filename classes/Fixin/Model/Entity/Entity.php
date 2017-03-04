<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\ToStringTrait;

abstract class Entity extends Prototype implements EntityInterface
{
    use ToStringTrait;

    protected const
        EXCEPTION_NOT_STORED_ENTITY = 'Not stored entity',
        THIS_SETS_LAZY = [
            self::OPTION_REPOSITORY => RepositoryInterface::class
        ];

    /**
     * @var EntityIdInterface|null
     */
    protected $entityId;

    /**
     * @var Entity[]
     */
    protected $outdatedSubEntities = array();

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @throws Exception\NotStoredEntityException
     * @return static
     */
    public function delete(): EntityInterface
    {
        if ($this->isStored()) {
            $this->entityId->deleteEntity();
            $this->entityId = null;

            return $this;
        }

        throw new Exception\NotStoredEntityException(static::EXCEPTION_NOT_STORED_ENTITY);
    }

    public function getEntityId(): ?EntityIdInterface
    {
        return $this->entityId;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    public function isStored(): bool
    {
        return isset($this->entityId);
    }

    /**
     * @return static
     */
    public function refresh(): EntityInterface
    {
        $this->getRepository()->refresh($this);

        return $this;
    }

    /**
     * @return static
     */
    public function save(): EntityInterface
    {
        $this->entityId = $this->getRepository()->save($this);

        $this
            ->refresh()
            ->saveSubEntities();

        return $this;
    }

    protected function saveSubEntities(): void
    {
        // Delete outdated entities
        $repositories = new \SplObjectStorage();
        foreach ($this->outdatedSubEntities as $entity) {
            $repositories[$entity->getRepository()] = $entity->getEntityId();
        }

        foreach ($repositories as $repository) {
            $repository->deleteByIds($repositories[$repository]);
        }

        $this->outdatedSubEntities = [];
    }

    protected function setEntityId(EntityIdInterface $entityId): void
    {
        $this->entityId = $entityId;
    }
}
