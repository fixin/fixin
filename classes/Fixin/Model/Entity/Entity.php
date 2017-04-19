<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\ToStringTrait;
use Fixin\Support\Types;

abstract class Entity extends Prototype implements EntityInterface
{
    use ToStringTrait;

    protected const
        NOT_STORED_ENTITY_EXCEPTION = 'Not stored entity',
        THIS_SETS = [
            self::ENTITY_ID => [EntityIdInterface::class, Types::NULL],
            self::REPOSITORY => [self::LAZY_LOADING => RepositoryInterface::class]
        ];

    /**
     * @var EntityIdInterface|null
     */
    protected $entityId;

    /**
     * @var EntityInterface[]
     */
    protected $outdatedSubEntities = array();

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @return $this
     * @throws Exception\NotStoredEntityException
     */
    public function delete(): EntityInterface
    {
        if ($this->isStored()) {
            $this->entityId->deleteEntity();
            $this->entityId = null;

            return $this;
        }

        throw new Exception\NotStoredEntityException(static::NOT_STORED_ENTITY_EXCEPTION);
    }

    public function getEntityId(): ?EntityIdInterface
    {
        return $this->entityId;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::REPOSITORY);
    }

    public function isStored(): bool
    {
        return isset($this->entityId);
    }

    /**
     * @return $this
     */
    public function refresh(): EntityInterface
    {
        $this->getRepository()->refresh($this);

        return $this;
    }

    /**
     * @return $this
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
}
