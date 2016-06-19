<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface RepositoryRequestInterface extends PrototypeInterface {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Delete
     *
     * @return int
     */
    public function delete(): int;

    /**
     * First record
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
     * Update
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;

    /*
     * avg
     * column
     * count
     * between
     * distinct
     * exists
     * first
     * having
     * groupBy
     * in
     * insertInto
     * join
     * leftJoin
     * max
     * min
     * notBetween
     * notIn
     * notNull
     * null
     * orderBy
     * sum
     * union
     * unionAll
     * value
     */
}