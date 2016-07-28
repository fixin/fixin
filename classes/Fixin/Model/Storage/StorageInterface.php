<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Repository\RepositoryRequestInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface {

    /**
     * Delete
     *
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function delete(RepositoryRequestInterface $request): int;

    /**
     * Insert
     *
     * @param array $set
     * @return int
     */
    public function insert(array $set): int;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @param StorageRequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): int;

    /**
     * Select
     *
     * @param RepositoryRequestInterface $request
     * @return StorageResultInterface
     */
    public function select(RepositoryRequestInterface $request): StorageResultInterface;

    /**
     * Update
     *
     * @param array $set
     * @param StorageRequestInterface $request
     * @return int
     */
    public function update(array $set, RepositoryRequestInterface $request): int;
}