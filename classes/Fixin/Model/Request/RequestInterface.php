<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface RequestInterface extends PrototypeInterface {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Delete
     *
     * @return int
     */
    public function delete(): int;

    /**
     * First entity
     *
     * @return EntityInterface|null
     */
    public function first();

    /**
     * Get entities
     *
     * @return EntitySetInterface
     */
    public function get(): EntitySetInterface;

    /**
     * Get raw data
     *
     * @return StorageResultInterface
     */
    public function getRawData(): StorageResultInterface;

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Limit count
     *
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self;

    /**
     * Update
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;

    /*
     * avg
     * column
     * value
     * count
     * max
     * min
     * sum
     *
     * distinct
     * columns?
     * having
     * groupBy
     * insertInto
     * join
     * leftJoin
     * orderBy
     * union
     * unionAll
     */
}