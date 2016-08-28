<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface GrammarInterface extends ResourceInterface {

    /**
     * Delete
     *
     * @param RequestInterface $request
     * @return string
     */
    public function delete(RequestInterface $request): string;

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
     * @param RequestInterface $request
     * @return string
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): string;

    /**
     * Select
     *
     * @param RequestInterface $request
     * @return string
     */
    public function select(RequestInterface $request): string;

    /**
     * Update
     *
     * @param array $set
     * @param RequestInterface $request
     * @return string
     */
    public function update(array $set, RequestInterface $request): string;
}