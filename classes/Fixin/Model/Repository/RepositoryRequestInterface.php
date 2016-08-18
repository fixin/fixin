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
     * Add an or where
     *
     * @param array|\Closure|self $where
     * @return self
     */
    public function orWhere($where): self;

    /**
     * Add an or between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function orWhereBetween(string $identifier, $min, $max): self;

    /**
     * Add an or where compare
     *
     * @param number|string|self|\Closure $left
     * @param string $operator
     * @param number|string|self|\Closure|array $right
     * @return self
     */
    public function orWhereCompare($left, string $operator, $right): self;

    /**
     * Add an or where exists
     *
     * @param self $request
     * @return self
     */
    public function orWhereExists(self $request): self;

    /**
     * Add an or where in
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function orWhereIn(string $identifier, array $values): self;

    /**
     * Add an or not between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function orWhereNotBetween(string $identifier, $min, $max): self;

    /**
     * Add an or where not exists
     *
     * @param self $request
     * @return self
     */
    public function orWhereNotExists(self $request): self;

    /**
     * Add an or where not in
     *
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function orWhereNotIn(string $identifier, array $values): self;

    /**
     * Add an or where not null
     *
     * @param string $identifier
     * @return self
     */
    public function orWhereNotNull(string $identifier): self;

    /**
     * Add an or null
     *
     * @param string $identifier
     * @return self
     */
    public function orWhereNull(string $identifier): self;

    /**
     * Update
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;

    /**
     * Add an and where
     *
     * @param array|\Closure|self $where
     * @return self
     */
    public function where($where): self;

    /**
     * Add an and between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function whereBetween(string $identifier, $min, $max): self;

    /**
     * Add an and where compare
     *
     * @param number|string|self|\Closure $left
     * @param string $operator
     * @param number|string|self|\Closure|array $right
     * @return self
     */
    public function whereCompare($left, string $operator, $right): self;

    /**
     * Add an and where exists
     *
     * @param self $request
     * @return self
     */
    public function whereExists(self $request): self;

    /**
     * Add an and where in
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function whereIn(string $identifier, array $values): self;

    /**
     * Add an and not between
     *
     * @param string $identifier
     * @param number|string $min
     * @param number|string $max
     * @return self
     */
    public function whereNotBetween(string $identifier, $min, $max): self;

    /**
     * Add an and where not exists
     *
     * @param self $request
     * @return self
     */
    public function whereNotExists(self $request): self;

    /**
     * Add an and where not in
     *
     * @param string $identifier
     * @param array $values
     * @return self
     */
    public function whereNotIn(string $identifier, array $values): self;

    /**
     * Add an and where not null
     *
     * @param string $identifier
     * @return self
     */
    public function whereNotNull(string $identifier): self;

    /**
     * Add an and null
     *
     * @param string $identifier
     * @return self
     */
    public function whereNull(string $identifier): self;

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