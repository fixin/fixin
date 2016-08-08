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
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    const OPTION_AUTO_INCREMENT_COLUMN = 'autoIncrementColumn';
    const OPTION_ENTITY_PROTOTYPE = 'entityPrototype';
    const OPTION_NAME = 'name';
    const OPTION_PRIMARY_KEY = 'primaryKey';
    const OPTION_STORAGE = 'storage';

    /**
     * All entities
     *
     * @return EntitySetInterface
     */
    public function all(): EntitySetInterface;

    /**
     * Create entity for the repository
     *
     * @return EntityInterface
     */
    public function create(): EntityInterface;

    /**
     * Create entity ID
     *
     * @param array|int|string ...$entityId
     * @return EntityIdInterface
     */
    public function createId(...$entityId): EntityIdInterface;

    /**
     * Delete
     *
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function delete(RepositoryRequestInterface $request): int;

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
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): int;

    /**
     * New request
     *
     * @return RepositoryRequestInterface
     */
    public function request(): RepositoryRequestInterface;

    /**
     * Select entities
     *
     * @param RepositoryRequestInterface $request
     * @return EntitySetInterface
     */
    public function selectEntities(RepositoryRequestInterface $request): EntitySetInterface;

    /**
     * Select raw data
     *
     * @param RepositoryRequestInterface $request
     * @return StorageResultInterface
     */
    public function selectRawData(RepositoryRequestInterface $request): StorageResultInterface;

    /**
     * Update
     *
     * @param array $set
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function update(array $set, RepositoryRequestInterface $request): int;
}