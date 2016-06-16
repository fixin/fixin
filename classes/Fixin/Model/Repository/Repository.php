<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Base\Storage\StorageInterface;
use Fixin\Exception\InvalidArgumentException;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Resource\Resource;
use Fixin\Resource\ResourceManagerInterface;

class Repository extends Resource implements RepositoryInterface {

    const DEFAULT_ID_PROTOTYPE = 'Base\Model\Entity\EntityId';
    const EXCEPTION_INVALID_ID = "Invalid ID";
    const EXCEPTION_INVALID_NAME = "Invalid name '%s'";
    const NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
    const THIS_REQUIRES = [
        self::OPTION_ENTITY_ID_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_ENTITY_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_NAME => self::TYPE_STRING,
        self::OPTION_PRIMARY_KEY => self::TYPE_ARRAY,
        self::OPTION_STORAGE => self::TYPE_INSTANCE,
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_ENTITY_ID_PROTOTYPE => EntityIdInterface::class,
        self::OPTION_ENTITY_PROTOTYPE => EntityInterface::class,
        self::OPTION_STORAGE => StorageInterface::class
    ];

    /**
     * @var EntityIdInterface|false|null
     */
    protected $entityIdPrototype;

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
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        if (!isset($options[static::OPTION_ENTITY_ID_PROTOTYPE])) {
            $options[static::OPTION_ENTITY_ID_PROTOTYPE] = static::DEFAULT_ID_PROTOTYPE;
        }

        parent::__construct($container, $options, $name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::createEntity()
     */
    public function createEntity(): EntityInterface {
        return clone $this->getEntityPrototype();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::createEntityId($entityId)
     */
    public function createEntityId(...$entityId): EntityIdInterface {
        $columnCount = count($this->primaryKey);

        // Array
        if (is_array($entityId[0])) {
            $entityId = array_intersect_key(array_flip($this->primaryKey), $entityId);

            if (count($entityId) === $columnCount) {
                return $this->createIdForArray($entityId);
            }

            throw new InvalidArgumentException(static::EXCEPTION_INVALID_ID);
        }

        // List
        if (count($entityId) === $columnCount) {
            return $this->createEntityIdWithArray(array_combine($this->primaryKey, $entityId));
        }

        throw new InvalidArgumentException(static::EXCEPTION_INVALID_ID);
    }

    /**
     * Create entity ID with array
     *
     * @param array $entityId
     * @return EntityIdInterface
     */
    private function createEntityIdWithArray(array $entityId): EntityIdInterface {
        return $this->getEntityIdPrototype()->withOptions([
            EntityIdInterface::OPTION_ENTITY_ID => $entityId
        ]);
    }

    /**
     * Get entity ID prototype
     *
     * @return EntityIdInterface
     */
    protected function getEntityIdPrototype(): EntityIdInterface {
        return $this->entityIdPrototype ?: $this->loadLazyProperty(static::OPTION_ENTITY_ID_PROTOTYPE, [
            EntityInterface::OPTION_REPOSITORY => $this
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
     * Get storage instance
     *
     * @return StorageInterface
     */
    protected function getStorage(): StorageInterface {
        return $this->storage ?: $this->loadLazyProperty(static::OPTION_STORAGE);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::saveEntity($entity)
     */
    public function saveEntity(EntityInterface $entity): RepositoryInterface {
        $this->getStorage()->save($entity);

        return $this;
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