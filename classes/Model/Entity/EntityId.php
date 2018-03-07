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

class EntityId extends Prototype implements EntityIdInterface
{
    protected const
        SEPARATOR = ',',
        THIS_SETS = [
            self::ENTITY_ID => self::USING_SETTER,
            self::REPOSITORY => [self::LAZY_LOADING => RepositoryInterface::class]
        ];

    /**
     * @var array
     */
    protected $entityId = [];

    /**
     * @var RepositoryInterface|false
     */
    protected $repository;

    /**
     * @var string
     */
    protected $string = '';

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * @inheritDoc
     */
    public function deleteEntity(): bool
    {
        $request = $this->getRepository()->createRequest();
        $request->getWhere()->items($this->entityId);

        return $request->delete() > 0;
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy(): array
    {
        return $this->entityId;
    }

    /**
     * @inheritDoc
     */
    public function getEntity(): ?EntityInterface
    {
        return $this->getRepository()->getById($this);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::REPOSITORY);
    }

    /**
     * Set entity ID
     *
     * @param array $entityId
     */
    protected function setEntityId(array $entityId): void
    {
        $this->entityId = $entityId;
        $this->string = implode(static::SEPARATOR, $entityId);
    }
}
