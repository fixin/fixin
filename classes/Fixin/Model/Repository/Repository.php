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
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Support\Arrays;

class Repository extends RepositoryBase {

    const
        EXCEPTION_INVALID_ID = "Invalid ID",
        EXCEPTION_INVALID_REQUEST = "Invalid request, repository mismatch '%s' '%s'",
        PROTOTYPE_ENTITY_ID = 'Model\Entity\EntityId',
        PROTOTYPE_ENTITY_SET = 'Model\Entity\EntitySet',
        PROTOTYPE_EXPRESSION = 'Model\Request\Expression',
        PROTOTYPE_REQUEST = 'Model\Request\Request';

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::create()
     */
    public function create(): EntityInterface {
        return clone $this->getEntityPrototype();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::createExpression($expression, $parameters)
     */
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface {
        return $this->container->clonePrototype(static::PROTOTYPE_EXPRESSION, [
            ExpressionInterface::OPTION_EXPRESSION => $expression,
            ExpressionInterface::OPTION_PARAMETERS => $parameters
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::createId($entityId)
     */
    public function createId(...$entityId): EntityIdInterface {
        $columnCount = count($this->primaryKey);

        // Array
        if (is_array($entityId[0])) {
            $entityId = array_intersect_key($entityId[0], array_flip($this->primaryKey));

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
        return $this->container->clonePrototype(static::PROTOTYPE_ENTITY_ID, [
            EntityIdInterface::OPTION_ENTITY_ID => $entityId,
            EntityIdInterface::OPTION_REPOSITORY => $this
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::createRequest()
     */
    public function createRequest(): RequestInterface {
        return $this->container->clonePrototype(static::PROTOTYPE_REQUEST, [
            RequestInterface::OPTION_REPOSITORY => $this
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::delete($request)
     */
    public function delete(RequestInterface $request): int {
        $this->validateRequest($request);

        return $this->getStorage()->delete($request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::exists($request)
     */
    public function exists(RequestInterface $request): bool {
        $this->validateRequest($request);

        return $this->getStorage()->exists($request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::insert($set)
     */
    public function insert(array $set): EntityIdInterface {
        if ($this->getStorage()->insert($this, $set)) {
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
        $this->validateRequest($request);

        return $this->getStorage()->insertInto($repository, $request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::insertMultiple($rows)
     */
    public function insertMultiple(array $rows): int {
        return $this->getStorage()->insertMultiple($this, $rows);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::select($request)
     */
    public function select(RequestInterface $request): EntitySetInterface {
        $fetchRequest = clone $request;
        $fetchRequest->setColumns($fetchRequest->isIdFetchEnabled() ? $this->primaryKey : []);

        return $this->container->clonePrototype(static::PROTOTYPE_ENTITY_SET, [
            EntitySetInterface::OPTION_REPOSITORY => $this,
            EntitySetInterface::OPTION_ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::OPTION_STORAGE_RESULT => $this->selectRawData($fetchRequest),
            EntitySetInterface::OPTION_ID_FETCH_MODE => $fetchRequest->isIdFetchEnabled()
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectAll()
     */
    public function selectAll(): EntitySetInterface {
        return $this->createRequest()->fetch();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectById($id)
     */
    public function selectById(EntityIdInterface $id) {
        $entities = $this->getEntityCache()->getByIds([$id]);

        return reset($entities);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectByIds($ids)
     */
    public function selectByIds(array $ids): EntitySetInterface {
        return $this->container->clonePrototype(static::PROTOTYPE_ENTITY_SET, [
            EntitySetInterface::OPTION_REPOSITORY => $this,
            EntitySetInterface::OPTION_ENTITY_CACHE => $this->getEntityCache(),
            EntitySetInterface::OPTION_ITEMS => $this->getEntityCache()->getByIds($ids)
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectColumn($request)
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface {
        return $this->getStorage()->selectColumn($request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::selectRawData($request)
     */
    public function selectRawData(RequestInterface $request): StorageResultInterface {
        return $this->getStorage()->select($request);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RepositoryInterface::update($set, $request)
     */
    public function update(array $set, RequestInterface $request): int {
        $this->validateRequest($request);

        return  $this->getStorage()->update($set, $request);
    }

    /**
     * Validate request
     *
     * @param RequestInterface $request
     */
    protected function validateRequest(RequestInterface $request) {
        if ($request->getRepository() === $this) {
            return;
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_REQUEST, $this->getName(), $request->getRepository()->getName()));
    }
}