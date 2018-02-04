<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Dictionary;

use Fixin\Resource\ResourceInterface;

interface DictionaryInterface extends ResourceInterface
{
    /**
     * Delete all items
     *
     * @return $this
     */
    public function clear(): self;

    /**
     * Decrement value of a numeric item
     *
     * @param string $key
     * @param int $step
     * @return int
     */
    public function decrement(string $key, int $step = 1): int;

    /**
     * Delete an item
     *
     * @param string $key
     * @return $this
     */
    public function delete(string $key): self;

    /**
     * Delete multiple items
     *
     * @param array $keys
     * @return $this
     */
    public function deleteMultiple(array $keys): self;

    /**
     * Retrieve an item
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Retrieve multiple items
     *
     * @param array $keys
     * @return array
     */
    public function getMultiple(array $keys): array;

    /**
     * Increment value of a numeric item
     *
     * @param string $key
     * @param int $step
     * @return int
     */
    public function increment(string $key, int $step = 1): int;

    /**
     * Store an item
     *
     * @param string $key
     * @param $value
     * @param int $seconds
     * @return $this
     */
    public function set(string $key, $value, int $seconds = 0): self;

    /**
     * Store multiple items
     *
     * @param array $items
     * @param int $seconds
     * @return $this
     */
    public function setMultiple(array $items, int $seconds = 0): self;
}
