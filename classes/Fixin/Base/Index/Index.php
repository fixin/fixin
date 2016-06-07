<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Index;

use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Exception\RuntimeException;
use Fixin\Resource\Prototype;
use Fixin\Support\Arrays;

class Index extends Prototype implements IndexInterface {

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
    protected $filename = '';

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
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::clear()
     */
    public function clear(): IndexInterface {
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
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::flush()
     */
    public function flush(): IndexInterface {
        if ($this->dirty) {
            $this->save();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOf($value)
     */
    public function getKeysOf($value): array {
        return array_slice($this->keys, $start = $this->findIndex($value, -1), $this->findIndex($value, 0) - $start);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOfGreaterThan($value)
     */
    public function getKeysOfGreaterThan($value): array {
        return array_slice($this->keys, $this->findIndex($value, 0));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOfGreaterThanOrEqual($value)
     */
    public function getKeysOfGreaterThanOrEqual($value): array {
        return array_slice($this->keys, $this->findIndex($value, -1));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOfInterval($beginValue, $endValue)
     */
    public function getKeysOfInterval($beginValue, $endValue): array {
        return array_slice($this->keys, $start = $this->findIndex($beginValue, -1), $this->findIndex($endValue, 0) - $start);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOfLowerThan($value)
     */
    public function getKeysOfLowerThan($value): array {
        return array_slice($this->keys, 0, $this->findIndex($value, -1));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOfLowerThanOrEqual($value)
     */
    public function getKeysOfLowerThanOrEqual($value): array {
        return array_slice($this->keys, 0, $this->findIndex($value, 0));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getKeysOfValues($values)
     */
    public function getKeysOfValues(array $values): array {
        return array_intersect_key($this->keys, array_intersect($this->values, $values));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getValue($key)
     */
    public function getValue($key) {
        return (false !== $index = array_search($key, $this->keys)) ? $this->values[$index] : null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::getValues($keys)
     */
    public function getValues(array $keys): array {
        $filtered = array_intersect($this->keys, $keys);

        return array_combine($filtered, array_intersect_key($this->values, $filtered));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::insert($key, $value)
     */
    public function insert($key, $value): IndexInterface {
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
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::remove($key)
     */
    public function remove($key): IndexInterface {
        if (false !== $index = array_search($key, $this->keys)) {
            array_splice($this->keys, $index, 1);
            array_splice($this->values, $index, 1);

            $this->dirty = true;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Index\IndexInterface::rollback()
     */
    public function rollback(): IndexInterface {
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