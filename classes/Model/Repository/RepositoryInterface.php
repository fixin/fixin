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
use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface
{
    public const
        AUTO_INCREMENT_COLUMN = 'autoIncrementColumn',
        ENTITY_PROTOTYPE = 'entityPrototype',
        NAME = 'name',
        PRIMARY_KEY = 'primaryKey',
        STORAGE = 'storage';

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
     * Delete entities by IDs
     *
     * @param EntityIdInterface[] $ids
     * @return int
     */
    public function deleteByIds(array $ids): int;

    /**
     * Get name of auto-increment column
     *
     * @return null|string
     */
    public function getAutoIncrementColumn(): ?string;

    /**
     * Get entity by ID
     *
     * @param EntityIdInterface $id
     * @return EntityInterface|null
     */
    public function getById(EntityIdInterface $id): ?EntityInterface;

    /**
     * Get entities by IDs
     *
     * @param array $ids
     * @return EntitySetInterface
     */
    public function getByIds(array $ids): EntitySetInterface;

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
     * Insert row
     *
     * @param array $set
     * @return EntityIdInterface
     */
    public function insert(array $set): EntityIdInterface;

    /**
     * Insert into by request
     *
     * @param RepositoryInterface $repository
     * @param RequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int;

    /**
     * Insert multiple rows
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
     * Select column
     *
     * @param RequestInterface $request
     * @return StorageResultInterface
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface;

    /**
     * Determine if row exists for the request
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function selectExistsValue(RequestInterface $request): bool;

    /**
     * Select raw data
     *
     * @param RequestInterface $request
     * @return StorageResultInterface
     */
    public function selectRawData(RequestInterface $request): StorageResultInterface;

    /**
     * Convert value to date object
     *
     * @param $value
     * @return DateTimeImmutable|null
     */
    public function toDateTime($value): ?DateTimeImmutable;

    /**
     * Update by request
     *
     * @param array $set
     * @param RequestInterface $request
     * @return int
     */
    public function update(array $set, RequestInterface $request): int;
}
