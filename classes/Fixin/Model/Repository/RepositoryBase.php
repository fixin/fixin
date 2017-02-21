<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\Cache\CacheInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Resource\Resource;

abstract class RepositoryBase extends Resource implements RepositoryInterface
{
    protected const
        EXCEPTION_INVALID_NAME = "Invalid name '%s'",
        NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/',
        THIS_REQUIRES = [
            self::OPTION_ENTITY_CACHE => self::TYPE_INSTANCE,
            self::OPTION_ENTITY_PROTOTYPE => self::TYPE_INSTANCE,
            self::OPTION_NAME => self::TYPE_STRING,
            self::OPTION_PRIMARY_KEY => self::TYPE_ARRAY,
            self::OPTION_STORAGE => self::TYPE_INSTANCE,
        ],
        THIS_SETS_LAZY = [
            self::OPTION_ENTITY_CACHE => CacheInterface::class,
            self::OPTION_ENTITY_PROTOTYPE => EntityInterface::class,
            self::OPTION_STORAGE => StorageInterface::class
        ];

    /**
     * @var string
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
    protected $name = '';

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
        return $this->entityCache ?: $this->loadLazyProperty(static::OPTION_ENTITY_CACHE, [
            CacheInterface::OPTION_REPOSITORY => $this,
            CacheInterface::OPTION_ENTITY_PROTOTYPE => $this->getEntityPrototype()
        ]);
    }

    protected function getEntityPrototype(): EntityInterface
    {
        return $this->entityPrototype ?: $this->loadLazyProperty(static::OPTION_ENTITY_PROTOTYPE, [
            EntityInterface::OPTION_REPOSITORY => $this
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
        return $this->storage ?: $this->loadLazyProperty(static::OPTION_STORAGE);
    }

    protected function setAutoIncrementColumn(string $autoIncrementColumn): void
    {
        $this->autoIncrementColumn = $autoIncrementColumn;
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

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_NAME, $name));
    }

    /**
     * @param string[] $primaryKey
     */
    protected function setPrimaryKey(array $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }
}
