<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use DateTime;
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
        OPTION_AUTO_INCREMENT_COLUMN = 'autoIncrementColumn',
        OPTION_ENTITY_CACHE = 'entityCache',
        OPTION_ENTITY_PROTOTYPE = 'entityPrototype',
        OPTION_NAME = 'name',
        OPTION_PRIMARY_KEY = 'primaryKey',
        OPTION_STORAGE = 'storage';

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
    public function selectExists(RequestInterface $request): bool;
    public function selectRawData(RequestInterface $request): StorageResultInterface;
    public function update(array $set, RequestInterface $request): int;

    /**
     * Convert value to DateTime
     *
     * @param string|int $value
     */
//    public function convertValueToDateTime($value): ?DateTime; TODO
}
