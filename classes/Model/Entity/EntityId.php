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

    public function __toString(): string
    {
        return $this->string;
    }

    public function deleteEntity(): bool
    {
        $request = $this->getRepository()->createRequest();
        $request->getWhere()->items($this->entityId);

        return $request->delete() > 0;
    }

    public function getArrayCopy(): array
    {
        return $this->entityId;
    }

    public function getEntity(): ?EntityInterface
    {
        return $this->getRepository()->getById($this);
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::REPOSITORY);
    }

    protected function setEntityId(array $entityId): void
    {
        $this->entityId = $entityId;
        $this->string = implode(static::SEPARATOR, $entityId);
    }
}
