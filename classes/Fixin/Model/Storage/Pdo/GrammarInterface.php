<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Resource\ResourceInterface;
use Fixin\Model\Repository\RepositoryRequestInterface;
use Fixin\Model\Repository\RepositoryInterface;

interface GrammarInterface extends ResourceInterface {

    /**
     * Delete
     *
     * @param RepositoryRequestInterface $request
     * @return string
     */
    public function delete(RepositoryRequestInterface $request): string;

    /**
     * Insert
     *
     * @param RepositoryInterface $repository
     * @param array $set
     * @return string
     */
    public function insert(RepositoryInterface $repository, array $set): string;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @param RepositoryRequestInterface $request
     * @return string
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): string;

    /**
     * Select
     *
     * @param RepositoryRequestInterface $request
     * @return string
     */
    public function select(RepositoryRequestInterface $request): string;

    /**
     * Update
     *
     * @param array $set
     * @param RepositoryRequestInterface $request
     * @return string
     */
    public function update(array $set, RepositoryRequestInterface $request): string;
}