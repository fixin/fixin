<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\Cache\CacheInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Resource\Resource;

abstract class RepositoryBase extends Resource implements RepositoryInterface
{
    protected const
        INVALID_NAME_EXCEPTION = "Invalid name '%s'",
        NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/',
        THIS_REQUIRES = [
            self::ENTITY_CACHE,
            self::ENTITY_PROTOTYPE,
            self::NAME,
            self::PRIMARY_KEY,
            self::STORAGE
        ],
        THIS_SETS = [
            self::AUTO_INCREMENT_COLUMN => [self::STRING_TYPE, self::NULL_TYPE],
            self::PRIMARY_KEY => self::ARRAY_TYPE
        ],
        THIS_SETS_LAZY = [
            self::ENTITY_CACHE => CacheInterface::class,
            self::ENTITY_PROTOTYPE => EntityInterface::class,
            self::STORAGE => StorageInterface::class
        ];

    /**
     * @var string|null
     */
    protected $autoIncrementColumn;

    /**
     * @var CacheInterface|false|null
     */
    protected $entityCache;

    /**
     * @var EntityInterface|false|null
     */
    protected $entityPrototype;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    protected $primaryKey = ['id'];

    /**
     * @var StorageInterface|false|null
     */
    protected $storage;

    public function getAutoIncrementColumn(): ?string
    {
        return $this->autoIncrementColumn;
    }

    protected function getEntityCache(): CacheInterface
    {
        return $this->entityCache ?: $this->loadLazyProperty(static::ENTITY_CACHE, [
            CacheInterface::REPOSITORY => $this,
            CacheInterface::ENTITY_PROTOTYPE => $this->getEntityPrototype()
        ]);
    }

    protected function getEntityPrototype(): EntityInterface
    {
        return $this->entityPrototype ?: $this->loadLazyProperty(static::ENTITY_PROTOTYPE, [
            EntityInterface::REPOSITORY => $this
        ]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    protected function getStorage(): StorageInterface
    {
        return $this->storage ?: $this->loadLazyProperty(static::STORAGE);
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function setName(string $name): void
    {
        if (preg_match(static::NAME_PATTERN, $name)) {
            $this->name = $name;

            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(static::INVALID_NAME_EXCEPTION, $name));
    }
}
