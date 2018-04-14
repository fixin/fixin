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
use Generator;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Arrays;
use Fixin\Support\Types;

class Repository extends Resource implements RepositoryInterface
{
    protected const
        ENTITY_ID_PROTOTYPE = '*\Model\Entity\EntityId',
        ENTITY_REFRESH_FAILURE_EXCEPTION = 'Entity refresh error',
        ENTITY_SET_PROTOTYPE = '*\Model\Entity\EntitySet',
        EXPRESSION_PROTOTYPE = '*\Model\Request\Expression',
        INVALID_ID_EXCEPTION = "Invalid ID",
        INVALID_NAME_EXCEPTION = "Invalid name '%s'",
        INVALID_REQUEST_EXCEPTION = "Invalid request, repository mismatch '%s' '%s'",
        NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/',
        NOT_STORED_ENTITY_EXCEPTION = 'Not stored entity',
        REQUEST_PROTOTYPE = '*\Model\Request\Request',
        THIS_SETS = [
            self::AUTO_INCREMENT_COLUMN => [Types::STRING, Types::NULL],
            self::ENTITY_PROTOTYPE => [self::LAZY_LOADING => EntityInterface::class],
            self::NAME => self::USING_SETTER,
            self::PRIMARY_KEY => Types::ARRAY,
            self::STORAGE => [self::LAZY_LOADING => StorageInterface::class]
        ];

    /**
     * @var string|null
     */
    protected $autoIncrementColumn;

    /**
     * @var EntityInterface|false
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
     * @var StorageInterface|false
     */
    protected $storage;

    /**
     * @inheritDoc
     */
    public function create(): EntityInterface
    {
        return clone $this->getEntityPrototype();
    }

    /**
     * @inheritDoc
     */
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->resourceManager->clone(static::EXPRESSION_PROTOTYPE, ExpressionInterface::class, [
            ExpressionInterface::EXPRESSION => $expression,
            ExpressionInterface::PARAMETERS => $parameters
        ]);
    }

    /**
     * @inheritDoc
     */
    // TODO: atgondolni, hogy kell-e igy, vagy csak a tömbös változat legyen
    public function createId(...$entityId): EntityIdInterface
    {
        $columnCount = count($this->primaryKey);

        // Array
        if (is_array($entityId[0])) {
            $entityId = array_intersect_key($entityId[0], array_flip($this->primaryKey));

            if (count($entityId) === $columnCount) {
                return $this->createIdByArray($entityId);
            }

            throw new Exception\InvalidArgumentException(static::INVALID_ID_EXCEPTION);
        }

        // List
        if (count($entityId) === $columnCount) {
            return $this->createIdByArray(array_combine($this->primaryKey, $entityId));
        }

        throw new Exception\InvalidArgumentException(static::INVALID_ID_EXCEPTION);
    }

    /**
     * Create ID by array
     *
     * @param array $entityId
     * @return EntityIdInterface
     */
    private function createIdByArray(array $entityId): EntityIdInterface
    {
        return $this->resourceManager->clone(static::ENTITY_ID_PROTOTYPE, EntityIdInterface::class, [
            EntityIdInterface::ENTITY_ID => $entityId,
            EntityIdInterface::REPOSITORY => $this
        ]);
    }

    /**
     * @inheritDoc
     */
    public function createRequest(): RequestInterface
    {
        return $this->resourceManager->clone(static::REQUEST_PROTOTYPE, RequestInterface::class, [
            RequestInterface::REPOSITORY => $this
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(RequestInterface $request): int
    {
        $this->validateRequest($request);

        return $this->getStorage()->delete($request);
    }

    /**
     * @inheritDoc
     */
    public function deleteByIds(array $ids): int
    {
        $request = $this->createRequest();
        $request->getWhere()->ids($ids);

        return $this->delete($request);
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementColumn(): ?string
    {
        return $this->autoIncrementColumn;
    }

    /**
     * @inheritDoc
     */
    public function getById(EntityIdInterface $id): ?EntityInterface
    {
        return $this->getByIds([$id])->current();
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): EntitySetInterface
    {
        $request = $this->createRequest();
        $request->getWhere()->id($ids);

        return $this->select($request);
    }

    /**
     * Get entity prototype
     *
     * @return EntityInterface
     */
    protected function getEntityPrototype(): EntityInterface
    {
        return $this->entityPrototype ?: $this->loadLazyProperty(static::ENTITY_PROTOTYPE, [
            EntityInterface::REPOSITORY => $this
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    /**
     * Get storage
     *
     * @return StorageInterface
     */
    protected function getStorage(): StorageInterface
    {
        return $this->storage ?: $this->loadLazyProperty(static::STORAGE);
    }

    /**
     * @inheritDoc
     */
    public function insert(array $set): EntityIdInterface
    {
        if ($this->getStorage()->insert($this, $set)) {
            $rowId = Arrays::intersectByKeyList($set, $this->primaryKey);

            if (isset($this->autoIncrementColumn)) {
                $rowId[$this->autoIncrementColumn] = $this->storage->getLastInsertValue();
            }

            return $this->createIdByArray($rowId);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int
    {
        $this->validateRequest($request);

        return $this->getStorage()->insertInto($repository, $request);
    }

    /**
     * @inheritDoc
     */
    public function insertMultiple(array $rows): int
    {
        return $this->getStorage()->insertMultiple($this, $rows);
    }

    /**
     * Iterate entities
     *
     * @param RequestInterface $request
     * @return Generator
     */
    protected function iterateEntities(RequestInterface $request): Generator
    {
        echo 'iterateEntities started', PHP_EOL;

        $entityPrototype = $this->getEntityPrototype();

        foreach ($this->selectRawData((clone $request)->setColumns([])) as $item) {
            echo "iterateEntities foreach: ", print_r($item), PHP_EOL;

            yield (clone $entityPrototype)->exchangeArray($item);
        }
    }

    /**
     * Iterate entity IDs
     *
     * @param RequestInterface $request
     * @return Generator
     */
    protected function iterateEntityIds(RequestInterface $request): Generator
    {
        echo 'iterateEntityIds started', PHP_EOL;

        foreach ($this->selectRawData((clone $request)->setColumns($this->primaryKey)) as $item) {
            echo "iterateEntityIds foreach: ", print_r($item), PHP_EOL;

            yield $this->createIdByArray($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function refresh(EntityInterface $entity): RepositoryInterface
    {
        if ($entity->isStored()) {
            $request = $this->createRequest();
            $request->getWhere()->id($entity->getEntityId());

            $data = $request->fetchRawData()->current();
            if ($data !== false) {
                $entity->exchangeArray($data);

                return $this;
            }

            throw new Exception\EntityRefreshFaultException(static::ENTITY_REFRESH_FAILURE_EXCEPTION);
        }

        throw new Exception\NotStoredEntityException(static::NOT_STORED_ENTITY_EXCEPTION);
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface $entity): EntityIdInterface
    {
        $set = $entity->collectSaveData();

        if ($oldId = $entity->getEntityId()) {
            $request = $this->createRequest();
            $request->getWhere()->id($oldId);

            $this->getStorage()->update($set, $request);

            $id = array_replace($oldId->getArrayCopy(), Arrays::intersectByKeyList($set, $this->primaryKey));

            return $id === $oldId->getArrayCopy() ? $oldId : $this->createIdByArray($id);
        }

        return $this->insert($set);
    }

    /**
     * @inheritDoc
     */
    public function select(RequestInterface $request): EntitySetInterface
    {
        return $this->resourceManager->clone(static::ENTITY_SET_PROTOTYPE, EntitySetInterface::class, [
            EntitySetInterface::ITERATOR => $request->isIdFetchEnabled() ? $this->iterateEntityIds($request) : $this->iterateEntities($request)
        ]);
    }

    /**
     * @inheritDoc
     */
    public function selectAll(): EntitySetInterface
    {
        return $this->createRequest()->fetch();
    }

    /**
     * @inheritDoc
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface
    {
        $this->validateRequest($request);

        return $this->getStorage()->selectColumn($request);
    }

    /**
     * @inheritDoc
     */
    public function selectExistsValue(RequestInterface $request): bool
    {
        $this->validateRequest($request);

        return $this->getStorage()->selectExistsValue($request);
    }

    /**
     * @inheritDoc
     */
    public function selectRawData(RequestInterface $request): StorageResultInterface
    {
        $this->validateRequest($request);

        return $this->getStorage()->select($request);
    }

    /**
     * Set name
     *
     * @param string $name
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

    /**
     * @inheritDoc
     */
    public function toDateTime($value): ?DateTimeImmutable
    {
        return $this->getStorage()->toDateTime($value);
    }

    /**
     * @inheritDoc
     */
    public function update(array $set, RequestInterface $request): int
    {
        $this->validateRequest($request);

        return $this->getStorage()->update($set, $request);
    }

    /**
     * Validate request
     *
     * @param RequestInterface $request
     * @throws Exception\InvalidRequestException
     */
    protected function validateRequest(RequestInterface $request): void
    {
        if ($request->getRepository() === $this) {
            return;
        }

        throw new Exception\InvalidRequestException(sprintf(static::INVALID_REQUEST_EXCEPTION, $this->getName(), $request->getRepository()->getName()));
    }
}
