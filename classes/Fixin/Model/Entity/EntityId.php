<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

class EntityId extends Prototype implements EntityIdInterface
{
    protected const
        SEPARATOR = ',',
        THIS_REQUIRES = [
            self::OPTION_REPOSITORY => self::TYPE_INSTANCE
        ],
        THIS_SETS_LAZY = [
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
        return $this->getRepository()->selectById($this);
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    protected function setEntityId(array $entityId): void
    {
        $this->entityId = $entityId;
        $this->string = implode(static::SEPARATOR, $entityId);
    }
}
