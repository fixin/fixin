<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    const
    OPTION_AUTO_INCREMENT_COLUMN = 'autoIncrementColumn',
    OPTION_ENTITY_CACHE = 'entityCache',
    OPTION_ENTITY_PROTOTYPE = 'entityPrototype',
    OPTION_NAME = 'name',
    OPTION_PRIMARY_KEY = 'primaryKey',
    OPTION_STORAGE = 'storage';

    /**
     * Create entity for the repository
     *
     * @return EntityInterface
     */
    public function create(): EntityInterface;

    /**
     * Create expression
     *
     * @param string $expression
     * @param array $parameters
     * @return ExpressionInterface
     */
    public function createExpression(string $expression, array $parameters = []): ExpressionInterface;

    /**
     * Create entity ID
     *
     * @param array|int|string ...$entityId
     * @return EntityIdInterface
     */
    public function createId(...$entityId): EntityIdInterface;

    /**
     * Create request
     *
     * @return RequestInterface
     */
    public function createRequest(): RequestInterface;

    /**
     * Delete
     *
     * @param RequestInterface $request
     * @return int
     */
    public function delete(RequestInterface $request): int;

    /**
     * Delete entities by ids
     *
     * @param EntityIdInterface[] $ids
     * @return int
     */
    public function deleteByIds(array $ids): int;

    /**
     * Exists
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function exists(RequestInterface $request): bool;

    /**
     * Get auto-increment column
     *
     * @return string|null
     */
    public function getAutoIncrementColumn();

    /**
     * Get name of the repository
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get primary key
     *
     * @return string[]
     */
    public function getPrimaryKey(): array;

    /**
     * Insert
     *
     * @param array $set
     * @return EntityIdInterface
     */
    public function insert(array $set): EntityIdInterface;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @param RequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int;

    /**
     * Insert multiple
     *
     * @param array $rows
     * @return int
     */
    public function insertMultiple(array $rows): int;

    /**
     * Refresh entity from storage
     *
     * @param EntityInterface $entity
     * @return RepositoryInterface
     */
    public function refresh(EntityInterface $entity): RepositoryInterface;

    /**
     * Save entity to storage
     *
     * @param EntityInterface $entity
     * @return EntityIdInterface
     */
    public function save(EntityInterface $entity): EntityIdInterface;

    /**
     * Select entities
     *
     * @param RequestInterface $request
     * @return EntitySetInterface
     */
    public function select(RequestInterface $request): EntitySetInterface;

    /**
     * Select all entities
     *
     * @return EntitySetInterface
     */
    public function selectAll(): EntitySetInterface;

    /**
     * Select entity by id
     *
     * @param EntityIdInterface $id
     * @return EntityInterface|null
     */
    public function selectById(EntityIdInterface $id);

    /**
     * Select entities by ids
     *
     * @param array $ids
     * @return EntitySetInterface
     */
    public function selectByIds(array $ids): EntitySetInterface;

    /**
     * Select column
     *
     * @param RequestInterface $request
     * @return StorageResultInterface
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface;

    /**
     * Select raw data
     *
     * @param RequestInterface $request
     * @return StorageResultInterface
     */
    public function selectRawData(RequestInterface $request): StorageResultInterface;

    /**
     * Update
     *
     * @param array $set
     * @param RequestInterface $request
     * @return int
     */
    public function update(array $set, RequestInterface $request): int;
}