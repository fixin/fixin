<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Arrays;

class Index extends Prototype {

    const EXCEPTION_FILENAME_NOT_SET = 'Filename not set';
    const EXCEPTION_INVALID_DATA = 'Invalid data';

    const KEY_KEYS = 'keys';
    const KEY_VALUES = 'values';

    /**
     * @var bool
     */
    protected $dirty = false;

    /**
     * @var FileSystemInterface|false|null
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Flush on destruction
     */
    public function __destruct() {
        if (isset($this->filename)) {
            $this->flush();
        }
    }

    /**
     * Clear all values
     *
     * @return self
     */
    public function clear() {
        $this->keys = [];
        $this->values = [];

        $this->dirty = true;

        return $this;
    }

    /**
     * Find index for a value based
     *
     * @param mixed $value
     * @param int $compareLimit
     * @return int
     */
    protected function findIndex($value, int $compareLimit): int {
        $begin = 0;
        $end = count($this->values) - 1;

        while ($begin <= $end) {
            $middle = intdiv($begin + $end, 2);

            if (($this->values[$middle] <=> $value) <= $compareLimit) {
                $begin = $middle + 1;

                continue;
            }

            $end = $middle - 1;
        }

        return $begin;
    }

    /**
     * Write data if dirty
     *
     * @return self
     */
    public function flush() {
        if ($this->dirty) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get keys of value
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOf($value): array {
        return array_slice($this->keys, $start = $this->findIndex($value, -1), $this->findIndex($value, 0) - $start);
    }

    /**
     * Get keys of greather than values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfGreaterThan($value): array {
        return array_slice($this->keys, $this->findIndex($value, 0));
    }

    /**
     * Get keys of greather than or equal values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfGreaterThanOrEqual($value): array {
        return array_slice($this->keys, $this->findIndex($value, -1));
    }

    /**
     * Get keys of values of interval
     *
     * @param mixed $beginValue
     * @param mixed $endValue
     * @return array
     */
    public function getKeysOfInterval($beginValue, $endValue): array {
        return array_slice($this->keys, $start = $this->findIndex($beginValue, -1), $this->findIndex($endValue, 0) - $start);
    }

    /**
     * Get keys of lower than values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfLowerThan($value): array {
        return array_slice($this->keys, 0, $this->findIndex($value, -1));
    }

    /**
     * Get keys of lower than or equal values
     *
     * @param mixed $value
     * @return array
     */
    public function getKeysOfLowerThanOrEqual($value): array {
        return array_slice($this->keys, 0, $this->findIndex($value, 0));
    }

    /**
     * Get keys of values
     *
     * @param array $values
     * @return array
     */
    public function getKeysOfValues(array $values): array {
        return array_intersect_key($this->keys, array_intersect($this->values, $values));
    }

    /**
     * Get value
     * @param mixed $key
     * @return NULL|mixed
     */
    public function getValue($key) {
        return (false !== $index = array_search($key, $this->keys)) ? $this->values[$index] : null;
    }

    /**
     * Get values
     *
     * @param array $keys
     * @return array
     */
    public function getValues(array $keys): array {
        $filtered = array_intersect($this->keys, $keys);

        return array_combine($filtered, array_intersect_key($this->values, $filtered));
    }

    /**
     * Insert key
     *
     * @param mixed $key
     * @param mixed $value
     * @return self
     */
    public function insert($key, $value) {
        $index = $this->findIndex($value, -1);

        array_splice($this->keys, $index, 0, [$key]);
        array_splice($this->values, $index, 0, [$value]);

        $this->dirty = true;

        return $this;
    }

    /**
     * Load data from the file
     *
     * @throws RuntimeException
     * @return self
     */
    protected function load() {
        if (!$this->filename) {
            throw new RuntimeException(static::EXCEPTION_FILENAME_NOT_SET);
        }

        $data = unserialize($this->fileSystem->get($this->filename), ['allowed_classes' => false]);
        if (!is_array($data) || !$this->loadArray($data)) {
            throw new RuntimeException(static::EXCEPTION_INVALID_DATA);
        }

        $this->dirty = false;

        return $this;
    }

    /**
     * Load data from array
     *
     * @param array $data
     * @return bool
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function loadArray(array $data): bool {
        // Value check
        $keys = Arrays::arrayForKey($data, static::KEY_KEYS);
        $values = Arrays::arrayForKey($data, static::KEY_VALUES);

        if (is_null($keys) || is_null($values) || count($keys) !== count($values)) {
            return false;
        }

        // Load
        $this->keys = $keys;
        $this->values = $values;

        return true;
    }

    /**
     * Remove key
     *
     * @param mixed $key
     * @return self
     */
    public function remove($key) {
        if (false !== $index = array_search($key, $this->keys)) {
            array_splice($this->keys, $index, 1);
            array_splice($this->values, $index, 1);

            $this->dirty = true;
        }

        return $this;
    }

    /**
     * Rollback modifications to the last saved state
     *
     * @return self
     */
    public function rollback() {
        $this->load();

        return $this;
    }

    /**
     * Save data
     *
     * @throws RuntimeException
     * @return self
     */
    protected function save() {
        if (!$this->filename) {
            throw new RuntimeException(static::EXCEPTION_FILENAME_NOT_SET);
        }

        $data = [
            static::KEY_KEYS => $this->keys,
            static::KEY_VALUES => $this->values
        ];

        $this->fileSystem->putWithLock($this->filename, serialize($data));

        $this->dirty = false;

        return $this;
    }

    /**
     * Set filename
     *
     * @param string $filename
     */
    protected function setFilename(string $filename) {
        $this->filename = $filename;
    }
}