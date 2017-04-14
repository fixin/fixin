<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Repository;

use DateTimeImmutable;
use Fixin\Model\Entity\Cache\CacheInterface;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Arrays;

class Repository extends Resource implements RepositoryInterface
{
    protected const
        ENTITY_ID_PROTOTYPE = 'Model\Entity\EntityId',
        ENTITY_REFRESH_ERROR_EXCEPTION = 'Entity refresh error',
        ENTITY_SET_PROTOTYPE = 'Model\Entity\EntitySet',
        EXPRESSION_PROTOTYPE = 'Model\Request\Expression',
        INVALID_ID_EXCEPTION = "Invalid ID",
        INVALID_NAME_EXCEPTION = "Invalid name '%s'",
        INVALID_REQUEST_EXCEPTION = "Invalid request, repository mismatch '%s' '%s'",
        NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/',
        NOT_STORED_ENTITY_EXCEPTION = 'Not stored entity',
        REQUEST_PROTOTYPE = 'Model\Request\Request',
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

    public function create(): EntityInterface
    {
        return clone $this->getEntityPrototype();
    }

    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->resourceManager->clone(static::EXPRESSION_PROTOTYPE, ExpressionInterface::class, [
            ExpressionInterface::EXPRESSION => $expression,
            ExpressionInterface::PARAMETERS => $parameters
        ]);
    }

    public function createId(...$entityId): EntityIdInterface
    {
        $columnCount = count($this->primaryKey);

        // Array
        if (is_array($entityId[0])) {
            $entityId = array_intersect_key($entityId[0], array_flip($this->primaryKey));

            if (count($entityId) === $columnCount) {
                return $this->createIdWithArray($entityId);
            }

            throw new Exception\InvalidArgumentException(static::INVALID_ID_EXCEPTION);
        }

        // List
        if (count($entityId) === $columnCount) {
            return $this->createIdWithArray(array_combine($this->primaryKey, $entityId));
        }

        throw new Exception\InvalidArgumentException(static::INVALID_ID_EXCEPTION);
    }

    private function createIdWithArray(array $entityId): EntityIdInterface
    {
        return $this->resourceManager->clone(static::ENTITY_ID_PROTOTYPE, EntityIdInterface::class, [
            EntityIdInterface::ENTITY_ID => $entityId,
            EntityIdInterface::REPOSITORY => $this
        ]);
    }

    public function createRequest(): RequestInterface
    {
        return $this->resourceManager->clone(static::REQUEST_PROTOTYPE, RequestInterface::class, [
            RequestInterface::REPOSITORY => $this
        ]);
    }

    public function delete(RequestInterface $request): int
    {
        $this->validateRequest($request);

        if ($result = $this->getStorage()->delete($request)) {
            $this->getEntityCache()->invalidate();
        }

        return $result;
    }

    public function deleteByIds(array $ids): int
    {
        $request = $this->createRequest();
        $request->getWhere()->ids($ids);

        return $this->delete($request);
    }

    public function getAutoIncrementColumn(): ?string
    {
        return $this->autoIncrementColumn;
    }

    public function getById(EntityIdInterface $id): ?EntityInterface
    {
        $entities = $this->getEntityCache()->getByIds([$id]);

        return reset($entities);
    }

    public function getByIds(array $ids): EntitySetInterface
    {
        return $this->resourceManager->clone(static::ENTITY_SET_PROTOTYPE, EntitySetInterface::class, [
            EntitySetInterface::REPOSITORY => $this,
            EntitySetInterface::ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::ITEMS => $this->getEntityCache()->getByIds($ids)
        ]);
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

    public function getValueAsDateTime($value): ?DateTimeImmutable
    {
        return $this->getStorage()->getValueAsDateTime($value);
    }

    public function insert(array $set): EntityIdInterface
    {
        if ($this->getStorage()->insert($this, $set)) {
            $rowId = Arrays::intersectByKeyList($set, $this->primaryKey);

            if (isset($this->autoIncrementColumn)) {
                $rowId[$this->autoIncrementColumn] = $this->storage->getLastInsertValue();
            }

            return $this->createIdWithArray($rowId);
        }

        return null;
    }

    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int
    {
        $this->validateRequest($request);

        return $this->getStorage()->insertInto($repository, $request);
    }

    public function insertMultiple(array $rows): int
    {
        return $this->getStorage()->insertMultiple($this, $rows);
    }

    /**
     * @return $this
     * @throws Exception\EntityRefreshFaultException
     */
    public function refresh(EntityInterface $entity): RepositoryInterface
    {
        if ($entity->isStored()) {
            $request = $this->createRequest();
            $request->getWhere()->id($entity->getEntityId());
            $data = $request->fetchRawData()->current();

            if ($data !== false) {
                $entity->exchangeArray($data);
                $this->getEntityCache()->update($entity);

                return $this;
            }

            throw new Exception\EntityRefreshFaultException(static::ENTITY_REFRESH_ERROR_EXCEPTION);
        }

        throw new Exception\EntityRefreshFaultException(static::NOT_STORED_ENTITY_EXCEPTION);
    }

    public function save(EntityInterface $entity): EntityIdInterface
    {
        $set = $entity->collectSaveData();

        if ($oldId = $entity->getEntityId()) {
            $request = $this->createRequest();
            $request->getWhere()->id($oldId);
            $this->getStorage()->update($set, $request);

            $id = array_replace($oldId->getArrayCopy(), Arrays::intersectByKeyList($set, $this->primaryKey));
            if ($id === $oldId->getArrayCopy()) {
                return $oldId;
            }

            $this->getEntityCache()->remove($entity);

            return $this->createIdWithArray($id);
        }

        return $this->insert($set);
    }

    public function select(RequestInterface $request): EntitySetInterface
    {
        $fetchRequest = clone $request;
        $fetchRequest->setColumns($fetchRequest->isIdFetchEnabled() ? $this->primaryKey : []);

        return $this->resourceManager->clone(static::ENTITY_SET_PROTOTYPE, EntitySetInterface::class, [
            EntitySetInterface::REPOSITORY => $this,
            EntitySetInterface::ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::STORAGE_RESULT => $this->selectRawData($fetchRequest),
            EntitySetInterface::ID_FETCH_MODE => $fetchRequest->isIdFetchEnabled()
        ]);
    }

    public function selectAll(): EntitySetInterface
    {
        return $this->createRequest()->fetch();
    }

    public function selectColumn(RequestInterface $request): StorageResultInterface
    {
        return $this->getStorage()->selectColumn($request);
    }

    public function selectExistsValue(RequestInterface $request): bool
    {
        $this->validateRequest($request);

        return $this->getStorage()->selectExistsValue($request);
    }

    public function selectRawData(RequestInterface $request): StorageResultInterface
    {
        return $this->getStorage()->select($request);
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

    public function update(array $set, RequestInterface $request): int
    {
        $this->validateRequest($request);

        if ($result = $this->getStorage()->update($set, $request)) {
            $this->getEntityCache()->invalidate();
        }

        return $result;
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function validateRequest(RequestInterface $request): void
    {
        if ($request->getRepository() === $this) {
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(static::INVALID_REQUEST_EXCEPTION, $this->getName(), $request->getRepository()->getName()));
    }
}