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
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Resource;

class Repository extends Resource implements RepositoryInterface {

    const ENTITY_ID_PROTOTYPE = 'Model\Entity\EntityId';
    const EXCEPTION_INVALID_ID = "Invalid ID";
    const EXCEPTION_INVALID_NAME = "Invalid name '%s'";
    const NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
    const REQUEST_PROTOTYPE = 'Model\Request\Request';
    const THIS_REQUIRES = [
        self::OPTION_ENTITY_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_NAME => self::TYPE_STRING,
        self::OPTION_PRIMARY_KEY => self::TYPE_ARRAY,
        self::OPTION_STORAGE => self::TYPE_INSTANCE,
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_ENTITY_PROTOTYPE => EntityInterface::class,
        self::OPTION_STORAGE => StorageInterface::class
    ];

    /**
     * @var string
     */
    protected $autoIncrementColumn;

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
     * @see \Fixin\Model\Repository\RepositoryInterface::all()
     */
    public function all(): EntitySetInterface {
        return $this->request()->get();
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
     * Create ID with array
     *
     * @param array $entityId
     * @return EntityIdInterface
     */
    private function createIdWithArray(array $entityId): EntityIdInterface {
        return $this->container->clonePrototype(static::ENTITY_ID_PROTOTYPE, [
            EntityIdInterface::OPTION_ENTITY_ID => $entityId
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::delete($request)
     */
    public function delete(RequestInterface $request): int {
        return $this->isValidRequest($request) && $this->getStorage()->delete($request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::getAutoIncrementColumn()
     */
    public function getAutoIncrementColumn() {
        return $this->autoIncrementColumn;
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
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::insert($set)
     */
    public function insert(array $set): EntityIdInterface {
        if ($this->getStorage()->insert($set)) {
            $rowId = Arrays::intersectByKeys($set, $this->primaryKey);

            if (isset($this->autoIncrementColumn)) {
                $rowId[$this->autoIncrementColumn] = $this->storage->getLastInsertValue();
            }

            return $this->createIdWithArray($rowId);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int {
        return $this->isValidRequest($request) && $this->getStorage()->insertInto($repository, $request);
    }

    /**
     * Check request validity
     *
     * @param RequestInterface $request
     * @return bool
     */
    protected function isValidRequest(RequestInterface $request): bool {
        return $request->getRepository() === $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::request()
     */
    public function request(): RequestInterface {
        return $this->container->clonePrototype(static::REQUEST_PROTOTYPE, [
            RequestInterface::OPTION_REPOSITORY => $this
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectEntities($request)
     */
    public function selectEntities(RequestInterface $request): EntitySetInterface {
        return null; // TODO implementation
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectRawData($request)
     */
    public function selectRawData(RequestInterface $request): StorageResultInterface {
        return $this->getStorage()->select($request);
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

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): int {
        return $this->isValidRequest($request) && $this->getStorage()->update($set, $request);
    }
}