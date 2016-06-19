<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Exception\InvalidArgumentException;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Resource\Resource;
use Fixin\Resource\ResourceManagerInterface;

class Repository extends Resource implements RepositoryInterface {

    const DEFAULT_ID_PROTOTYPE = 'Model\Entity\EntityId';
    const DEFAULT_REQUEST_PROTOTYPE = 'Model\Repository\RepositoryRequest';
    const EXCEPTION_INVALID_ID = "Invalid ID";
    const EXCEPTION_INVALID_NAME = "Invalid name '%s'";
    const NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
    const THIS_REQUIRES = [
        self::OPTION_ENTITY_ID_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_ENTITY_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_NAME => self::TYPE_STRING,
        self::OPTION_PRIMARY_KEY => self::TYPE_ARRAY,
        self::OPTION_REQUEST_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_STORAGE => self::TYPE_INSTANCE,
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_ENTITY_ID_PROTOTYPE => EntityIdInterface::class,
        self::OPTION_ENTITY_PROTOTYPE => EntityInterface::class,
        self::OPTION_REQUEST_PROTOTYPE => RepositoryRequestInterface::class,
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
     * @var RepositoryRequestInterface|false|null
     */
    protected $requestPrototype;

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
        parent::__construct($container, $options += [
            static::OPTION_ENTITY_ID_PROTOTYPE => static::DEFAULT_ID_PROTOTYPE,
            static::OPTION_REQUEST_PROTOTYPE => static::DEFAULT_REQUEST_PROTOTYPE
        ], $name);
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
                return $this->createIdWithArray($entityId);
            }

            throw new InvalidArgumentException(static::EXCEPTION_INVALID_ID);
        }

        // List
        if (count($entityId) === $columnCount) {
            return $this->createIdWithArray(array_combine($this->primaryKey, $entityId));
        }

        throw new InvalidArgumentException(static::EXCEPTION_INVALID_ID);
    }

    /**
     * Create entity ID with array
     *
     * @param array $entityId
     * @return EntityIdInterface
     */
    private function createIdWithArray(array $entityId): EntityIdInterface {
        return $this->getEntityIdPrototype()->withOptions([
            EntityIdInterface::OPTION_ENTITY_ID => $entityId
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::delete($request)
     */
    public function delete(RepositoryRequestInterface $request): int {
        return $this->isValidRequest($request) && $this->getStorage()->delete($request);
    }

    /**
     * Get entity ID prototype
     *
     * @return EntityIdInterface
     */
    protected function getEntityIdPrototype(): EntityIdInterface {
        return $this->entityIdPrototype ?: $this->loadLazyProperty(static::OPTION_ENTITY_ID_PROTOTYPE, [
            EntityIdInterface::OPTION_REPOSITORY => $this
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
     * Get request prototype
     *
     * @return RepositoryRequestInterface
     */
    protected function getRequestPrototype(): RepositoryRequestInterface {
        return $this->requestPrototype ?: $this->loadLazyProperty(static::OPTION_REQUEST_PROTOTYPE, [
            RepositoryRequestInterface::OPTION_REPOSITORY => $this
        ]);
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
     * @see \Fixin\Model\Repository\RepositoryInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): int {
        return $this->getStorage()->insertInto($repository, $request);
    }

    /**
     * Check request validity
     *
     * @param RepositoryRequestInterface $request
     * @return bool
     */
    protected function isValidRequest(RepositoryRequestInterface $request): bool {
        return $request->getRepository() === $this;
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
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::update($set, $request)
     */
    public function update(array $set, RepositoryRequestInterface $request): int {
        return $this->isValidRequest($request) && $this->getStorage()->update($set, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::where($where)
     */
    public function where($where): RepositoryRequestInterface {
        return (clone $this->getRequestPrototype());
    }
}