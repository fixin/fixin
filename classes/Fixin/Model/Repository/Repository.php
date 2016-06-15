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
use Fixin\Support\Arrays;

class Repository extends Resource implements RepositoryInterface {

    const CONFIGURATION_REQUIRES = [
        'entityPrototype' => 'instance',
        'name' => 'string',
        'primaryKey' => 'array',
        'storage' => 'instance',
    ];
    const EXCEPTION_INVALID_ID = "Invalid ID";
    const EXCEPTION_INVALID_NAME = "Invalid name '%s'";
    const NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

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
        $this->setLazyLoadingProperty('entityIdPrototype', EntityIdInterface::class, 'Base\Model\Entity\EntityId');

        parent::__construct($container, $options, $name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::create()
     */
    public function create(): EntityInterface {
        return clone $this->getEntityPrototype();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::createId($entityId)
     */
    public function createId(...$entityId): EntityIdInterface {
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
            return $this->createIdForArray(array_combine($this->primaryKey, $entityId));
        }

        throw new InvalidArgumentException(static::EXCEPTION_INVALID_ID);
    }

    /**
     * Create ID instance for array
     *
     * @param array $entityId
     * @return EntityIdInterface
     */
    private function createIdForArray(array $entityId): EntityIdInterface {
        return $this->getEntityIdPrototype()->withOptions([
            EntityIdInterface::OPTION_ENTITY_ID => $entityId
        ]);
    }

    /**
     * Get entity prototype instance
     *
     * @return EntityInterface
     */
    protected function getEntityPrototype(): EntityInterface {
        return $this->entityPrototype ?: $this->loadLazyProperty('entityPrototype', [
            EntityInterface::OPTION_REPOSITORY => $this
        ]);
    }

    /**
     * Get entity ID prototype instance
     *
     * @return EntityIdInterface
     */
    protected function getEntityIdPrototype(): EntityIdInterface {
        return $this->entityIdPrototype ?: $this->loadLazyProperty('entityIdPrototype', [
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
        return $this->storage ?: $this->loadLazyProperty('storage');
    }

    public function normalizeEntityId(array $entityId): array {




        $primaryKey = $this->getRepository()->getPrimaryKey();



        if (count($entityId) === count($primaryKey)) {
            $this->entityId = array_combine($primaryKey, $entityId);
        }


    }

    /**
     * Set entity prototype
     *
     * @param string|EntityInterface $entityPrototype
     */
    protected function setEntityPrototype($entityPrototype) {
        $this->setLazyLoadingProperty('entityPrototype', EntityInterface::class, $entityPrototype);
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

    /**
     * Set storage
     *
     * @param string|StorageInterface $storage
     */
    protected function setStorage($storage) {
        $this->setLazyLoadingProperty('storage', StorageInterface::class, $storage);
    }
}