<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Index;

use Fixin\Resource\PrototypeInterface;

interface IndexInterface extends PrototypeInterface
{
    /**
     * Clear all values
     *
     * @return $this
     */
    public function clear(): IndexInterface;

    /**
     * Delete value
     *
     * @param $key
     * @return $this
     */
    public function delete($key): IndexInterface;

    /**
     * Get keys of value
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOf($value): array;

    /**
     * Get keys of values that are greater than value
     *
     * @param $value
     * @return array
     */
    public function getKeysOfGreaterThan($value): array;

    /**
     * Get keys of values that are greater than or equal value
     *
     * @param $value
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
     * Get keys of values that are lower than value
     *
     * @param $value
     * @return array
     */
    public function getKeysOfLowerThan($value): array;

    /**
     * Get keys of values that are lower than or equal value
     *
     * @param $value
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
     * @return null|mixed
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
     * Revert to the last saved state
     *
     * @return $this
     */
    public function revert(): IndexInterface;

    /**
     * Write data if dirty
     *
     * @return $this
     */
    public function save(): IndexInterface;

    /**
     * Set value
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value): IndexInterface;
}
