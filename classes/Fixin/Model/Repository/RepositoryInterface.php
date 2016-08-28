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
use Fixin\Model\Request\RequestInterface;
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
     * @param RequestInterface $request
     * @return int
     */
    public function delete(RequestInterface $request): int;

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
     * New request
     *
     * @return RequestInterface
     */
    public function request(): RequestInterface;

    /**
     * Select entities
     *
     * @param RequestInterface $request
     * @return EntitySetInterface
     */
    public function selectEntities(RequestInterface $request): EntitySetInterface;

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