<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface {

    /**
     * Delete
     *
     * @param RequestInterface $request
     * @return int
     */
    public function delete(RequestInterface $request): int;

    /**
     * Get last insert value
     *
     * @return int
     */
    public function getLastInsertValue(): int;

    /**
     * Check existance
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function exists(RequestInterface $request): bool;

    /**
     * Insert
     *
     * @param RepositoryInterface $repository
     * @param array $set
     * @return int
     */
    public function insert(RepositoryInterface $repository, array $set): int;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @param StorageRequestInterface $request
     * @return int
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int;

    /**
     * Insert multiple
     *
     * @param RepositoryInterface $repository
     * @param array $rows
     * @return int
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): int;

    /**
     * Select
     *
     * @param RequestInterface $request
     * @return StorageResultInterface
     */
    public function select(RequestInterface $request): StorageResultInterface;

    /**
     * Select column
     *
     * @param RequestInterface $request
     * @return StorageResultInterface
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface;

    /**
     * Update
     *
     * @param array $set
     * @param StorageRequestInterface $request
     * @return int
     */
    public function update(array $set, RequestInterface $request): int;

    /**
     * Convert value to DateTime
     *
     * @param string|int $value
     * @return DateTime|null
     */
    public function valueToDateTime($value);
}