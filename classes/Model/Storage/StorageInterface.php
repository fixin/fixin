<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage;

use DateTimeImmutable;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface
{
    /**
     * Delete
     */
    public function delete(RequestInterface $request): int;

    /**
     * Get last insert value
     */
    public function getLastInsertValue(): int;

    /**
     * Insert
     */
    public function insert(RepositoryInterface $repository, array $set): int;

    /**
     * Insert into
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int;

    /**
     * Insert multiple
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): int;

    /**
     * Select
     */
    public function select(RequestInterface $request): StorageResultInterface;

    /**
     * Select column
     */
    public function selectColumn(RequestInterface $request): StorageResultInterface;

    /**
     * Select exists value
     */
    public function selectExistsValue(RequestInterface $request): bool;

    /**
     * Convert value to DateTime
     */
    public function toDateTime($value): ?DateTimeImmutable;

    /**
     * Update
     */
    public function update(array $set, RequestInterface $request): int;
}
