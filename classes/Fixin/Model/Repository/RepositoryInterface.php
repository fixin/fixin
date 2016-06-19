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
use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    const OPTION_ENTITY_ID_PROTOTYPE = 'entityIdPrototype';
    const OPTION_ENTITY_PROTOTYPE = 'entityPrototype';
    const OPTION_NAME = 'name';
    const OPTION_PRIMARY_KEY = 'primaryKey';
    const OPTION_REQUEST_PROTOTYPE = 'requestPrototype';
    const OPTION_STORAGE = 'storage';

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
     * Delete record(s)
     *
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function delete(RepositoryRequestInterface $request): int;

    /**
     * Get entities
     *
     * @param RepositoryRequestInterface $request
     * @return EntitySetInterface
     */
//     public function get(RepositoryRequestInterface $request): EntitySetInterface;

    /**
     * Get name of the repository
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Insert record(s) by request into repository
     *
     * @param RepositoryInterface $repository
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): int;

    /**
     * Update record(s)
     *
     * @param array $set
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function update(array $set, RepositoryRequestInterface $request): int;

    /**
     * New request instance
     *
     * @param mixed $where
     * @return RepositoryRequestInterface
     */
    public function where($where): RepositoryRequestInterface;
}