<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Repository;

use DateTime;
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
        EXCEPTION_ENTITY_REFRESH_ERROR = 'Entity refresh error',
        EXCEPTION_INVALID_ID = "Invalid ID",
        EXCEPTION_INVALID_REQUEST = "Invalid request, repository mismatch '%s' '%s'",
        EXCEPTION_NOT_STORED_ENTITY = 'Not stored entity',
        PROTOTYPE_ENTITY_ID = 'Model\Entity\EntityId',
        PROTOTYPE_ENTITY_SET = 'Model\Entity\EntitySet',
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression',
        PROTOTYPE_REQUEST = 'Model\Request\Request';

    public function create(): EntityInterface
    {
        return clone $this->getEntityPrototype();
    }

    public function createExpression(string $expression, array $parameters = []): ExpressionInterface
    {
        return $this->container->clone(static::PROTOTYPE_EXPRESSION, [
            ExpressionInterface::OPTION_EXPRESSION => $expression,
            ExpressionInterface::OPTION_PARAMETERS => $parameters
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

            throw new Exception\InvalidArgumentException(static::EXCEPTION_INVALID_ID);
        }

        // List
        if (count($entityId) === $columnCount) {
            return $this->createIdWithArray(array_combine($this->primaryKey, $entityId));
        }

        throw new Exception\InvalidArgumentException(static::EXCEPTION_INVALID_ID);
    }

    private function createIdWithArray(array $entityId): EntityIdInterface
    {
        return $this->container->clone(static::PROTOTYPE_ENTITY_ID, [
            EntityIdInterface::OPTION_ENTITY_ID => $entityId,
            EntityIdInterface::OPTION_REPOSITORY => $this
        ]);
    }

    public function createRequest(): RequestInterface
    {
        return $this->container->clone(static::PROTOTYPE_REQUEST, [
            RequestInterface::OPTION_REPOSITORY => $this
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
        return $this->container->clone(static::PROTOTYPE_ENTITY_SET, [
            EntitySetInterface::OPTION_REPOSITORY => $this,
            EntitySetInterface::OPTION_ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::OPTION_ITEMS => $this->getEntityCache()->getByIds($ids)
        ]);
    }

    public function getValueAsDateTime($value): ?DateTime
    {
        return $this->getStorage()->getValueAsDateTime($value);
    }

    public function insert(array $set): EntityIdInterface
    {
        if ($this->getStorage()->insert($this, $set)) {
            $rowId = Arrays::intersectByKeys($set, $this->primaryKey);

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
     * @return static
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

            throw new Exception\EntityRefreshFaultException(static::EXCEPTION_ENTITY_REFRESH_ERROR);
        }

        throw new Exception\EntityRefreshFaultException(static::EXCEPTION_NOT_STORED_ENTITY);
    }

    public function save(EntityInterface $entity): EntityIdInterface
    {
        $set = $entity->collectSaveData();

        if ($oldId = $entity->getEntityId()) {
            $request = $this->createRequest();
            $request->getWhere()->id($oldId);
            $this->getStorage()->update($set, $request);

            $id = array_replace($oldId->getArrayCopy(), Arrays::intersectByKeys($set, $this->primaryKey));
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

        return $this->container->clone(static::PROTOTYPE_ENTITY_SET, [
            EntitySetInterface::OPTION_REPOSITORY => $this,
            EntitySetInterface::OPTION_ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::OPTION_STORAGE_RESULT => $this->selectRawData($fetchRequest),
            EntitySetInterface::OPTION_ID_FETCH_MODE => $fetchRequest->isIdFetchEnabled()
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

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_REQUEST, $this->getName(), $request->getRepository()->getName()));
    }
}
