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
        ENTITY_CACHE = 'entityCache',
        ENTITY_PROTOTYPE = 'entityPrototype',
        NAME = 'name',
        PRIMARY_KEY = 'primaryKey',
        STORAGE = 'storage';

    /**
     * Create entity for the repository
     */
    public function create(): EntityInterface;

    public function createExpression(string $expression, array $parameters = []): ExpressionInterface;

    /**
     * Create entity ID
     *
     * @param array|int|string ...$entityId
     */
    public function createId(...$entityId): EntityIdInterface;

    public function createRequest(): RequestInterface;
    public function delete(RequestInterface $request): int;

    /**
     * Delete entities by ids
     *
     * @param EntityIdInterface[] $ids
     */
    public function deleteByIds(array $ids): int;

    public function getAutoIncrementColumn(): ?string;

    /**
     * Get entity by id
     */
    public function getById(EntityIdInterface $id): ?EntityInterface;

    /**
     * Get entities by ids
     */
    public function getByIds(array $ids): EntitySetInterface;

    public function getName(): string;

    /**
     * @return string[]
     */
    public function getPrimaryKey(): array;

    public function insert(array $set): EntityIdInterface;
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int;
    public function insertMultiple(array $rows): int;

    /**
     * Refresh entity from storage
     *
     * @return $this
     */
    public function refresh(EntityInterface $entity): RepositoryInterface;

    /**
     * Save entity to storage
     */
    public function save(EntityInterface $entity): EntityIdInterface;

    /**
     * Select entities
     */
    public function select(RequestInterface $request): EntitySetInterface;

    /**
     * Select all entities
     */
    public function selectAll(): EntitySetInterface;

    public function selectColumn(RequestInterface $request): StorageResultInterface;
    public function selectExistsValue(RequestInterface $request): bool;
    public function selectRawData(RequestInterface $request): StorageResultInterface;
    public function toDateTime($value): ?DateTimeImmutable;
    public function update(array $set, RequestInterface $request): int;
}
