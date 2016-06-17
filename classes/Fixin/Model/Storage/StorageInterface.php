<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage;

use Fixin\Model\Repository\RepositoryRequestInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface {

    /**
     * Delete record(s)
     *
     * @param RepositoryRequestInterface $request
     * @return int
     */
    public function delete(RepositoryRequestInterface $request): int;

    /**
     * Fetch data
     *
     * @param RepositoryRequestInterface $request
     * @return array
     */
    public function fetch(RepositoryRequestInterface $request): array;

    /**
     * Insert record(s) by request into repository
     *
     * @param RepositoryInterface $repository
     * @param StorageRequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): int;

    /**
     * Update record(s)
     *
     * @param array $set
     * @param StorageRequestInterface $request
     * @return int
     */
    public function update(array $set, RepositoryRequestInterface $request): int;
}