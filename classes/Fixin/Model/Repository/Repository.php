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
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Support\Arrays;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Repository extends RepositoryBase
{
    protected const
        ENTITY_ID_PROTOTYPE = 'Model\Entity\EntityId',
        ENTITY_REFRESH_ERROR_EXCEPTION = 'Entity refresh error',
        ENTITY_SET_PROTOTYPE = 'Model\Entity\EntitySet',
        EXPRESSION_PROTOTYPE = 'Model\Request\Expression',
        INVALID_ID_EXCEPTION = "Invalid ID",
        INVALID_REQUEST_EXCEPTION = "Invalid request, repository mismatch '%s' '%s'",
        NOT_STORED_ENTITY_EXCEPTION = 'Not stored entity',
        REQUEST_PROTOTYPE = 'Model\Request\Request';

    public function create(): EntityInterface
    {
        return clone $this->getEntityPrototype();
    }

    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->resourceManager->clone(static::EXPRESSION_PROTOTYPE, [
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
        return $this->resourceManager->clone(static::ENTITY_ID_PROTOTYPE, [
            EntityIdInterface::ENTITY_ID => $entityId,
            EntityIdInterface::REPOSITORY => $this
        ]);
    }

    public function createRequest(): RequestInterface
    {
        return $this->resourceManager->clone(static::REQUEST_PROTOTYPE, [
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

    public function getById(EntityIdInterface $id): ?EntityInterface
    {
        $entities = $this->getEntityCache()->getByIds([$id]);

        return reset($entities);
    }

    public function getByIds(array $ids): EntitySetInterface
    {
        return $this->resourceManager->clone(static::ENTITY_SET_PROTOTYPE, [
            EntitySetInterface::REPOSITORY => $this,
            EntitySetInterface::ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::ITEMS => $this->getEntityCache()->getByIds($ids)
        ]);
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

        return $this->resourceManager->clone(static::ENTITY_SET_PROTOTYPE, [
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
