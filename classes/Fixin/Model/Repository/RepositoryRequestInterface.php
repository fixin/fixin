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

    /**
     * Where
     *
     * @param array|\Closure $where
     * @return self
     */
    public function where($where): self;

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
     * exists
     * having
     * groupBy
     * insertInto
     * join
     * leftJoin
     * orderBy
     * union
     * unionAll
     *
     * between
     * in
     * notBetween
     * notIn
     * notNull
     * null
     * nested, nestedClose
     */
}