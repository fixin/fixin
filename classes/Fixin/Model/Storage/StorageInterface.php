<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage;

use DateTime;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface
{
    public function delete(RequestInterface $request): int;
    public function getLastInsertValue(): int;
    public function insert(RepositoryInterface $repository, array $set): int;
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): int;
    public function insertMultiple(RepositoryInterface $repository, array $rows): int;
    public function select(RequestInterface $request): StorageResultInterface;
    public function selectColumn(RequestInterface $request): StorageResultInterface;
    public function selectExists(RequestInterface $request): bool;
    public function update(array $set, RequestInterface $request): int;

    /**
     * Convert value to DateTime
     *
     * @param string|int $value
     */
//    public function valueToDateTime($value): ?\DateTime;
}
