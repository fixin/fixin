<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Index;

use Fixin\Resource\PrototypeInterface;

interface IndexInterface extends PrototypeInterface {

    /**
     * Clear all values
     *
     * @return self
     */
    public function clear(): IndexInterface;

    /**
     * Write data if dirty
     *
     * @return self
     */
    public function flush(): IndexInterface;

    /**
     * Get keys of value
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOf($value): array;

    /**
     * Get keys of greather than values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfGreaterThan($value): array;

    /**
     * Get keys of greather than or equal values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfGreaterThanOrEqual($value): array;

    /**
     * Get keys of values of interval
     *
     * @param mixed $beginValue
     * @param mixed $endValue
     * @return array
     */
    public function getKeysOfInterval($beginValue, $endValue): array;

    /**
     * Get keys of lower than values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfLowerThan($value): array;

    /**
     * Get keys of lower than or equal values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfLowerThanOrEqual($value): array;

    /**
     * Get keys of values
     *
     * @param array $values
     * @return array
     */
    public function getKeysOfValues(array $values): array;

    /**
     * Get value
     *
     * @param mixed $key
     * @return NULL|mixed
     */
    public function getValue($key);

    /**
     * Get values
     *
     * @param array $keys
     * @return array
     */
    public function getValues(array $keys): array;

    /**
     * Insert key
     *
     * @param mixed $key
     * @param mixed $value
     * @return self
     */
    public function insert($key, $value): IndexInterface;

    /**
     * Remove key
     *
     * @param mixed $key
     * @return self
     */
    public function remove($key): IndexInterface;

    /**
     * Rollback modifications to the last saved state
     *
     * @return self
     */
    public function rollback(): IndexInterface;
}