<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Exception\InvalidArgumentException;
use Fixin\Model\Entity\Cache\CacheInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Resource\Resource;

abstract class RepositoryBase extends Resource implements RepositoryInterface {

    const
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

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::getAutoIncrementColumn()
     */
    public function getAutoIncrementColumn() {
        return $this->autoIncrementColumn;
    }

    /**
     * Get entity cache
     *
     * @return CacheInterface
     */
    protected function getEntityCache(): CacheInterface {
        return $this->entityCache ?: $this->loadLazyProperty(static::OPTION_ENTITY_CACHE, [
            CacheInterface::OPTION_REPOSITORY => $this,
            CacheInterface::OPTION_ENTITY_PROTOTYPE => $this->getEntityPrototype()
        ]);
    }

    /**
     * Get entity prototype
     *
     * @return EntityInterface
     */
    protected function getEntityPrototype(): EntityInterface {
        return $this->entityPrototype ?: $this->loadLazyProperty(static::OPTION_ENTITY_PROTOTYPE, [
            EntityInterface::OPTION_REPOSITORY => $this
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::getName()
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::getPrimaryKey()
     */
    public function getPrimaryKey(): array {
        return $this->primaryKey;
    }

    /**
     * Get storage instance
     *
     * @return StorageInterface
     */
    protected function getStorage(): StorageInterface {
        return $this->storage ?: $this->loadLazyProperty(static::OPTION_STORAGE);
    }

    /**
     * Set auto-increment column
     *
     * @param string $autoIncrementColumn
     */
    protected function setAutoIncrementColumn(string $autoIncrementColumn) {
        $this->autoIncrementColumn = $autoIncrementColumn;
    }

    /**
     * Set name
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    protected function setName(string $name) {
        if (preg_match(static::NAME_PATTERN, $name)) {
            $this->name = $name;

            return;
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_NAME, $name));
    }

    /**
     * Set primary key
     *
     * @param string[] $primaryKey
     */
    protected function setPrimaryKey(array $primaryKey) {
        $this->primaryKey = $primaryKey;
    }
}