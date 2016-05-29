<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Resource\Resource;
use Fixin\Support\PrototypeInterface;

class Index extends Resource implements PrototypeInterface {

    const EXCEPTION_FILENAME_NOT_SET = 'Filename not set';
    const EXCEPTION_INVALID_DATA = 'Invalid data';
    const KEY_FILENAME = 'filename';
    const KEY_PRIMARY_KEYS = 'primaryKeys';
    const KEY_VALUES = 'values';

    /**
     * @var bool
     */
    protected $dirty = false;

    /**
     * @var FileSystemInterface
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $primaryKeys = [];

    /**
     * @var array
     */
    protected $values = [];

    public function __destruct() {
        $this->flush();
    }

    /**
     * Clear all values
     *
     * @return self
     */
    public function clear() {
        $this->primaryKeys = [];
        $this->values = [];

        $this->dirty = true;

        return $this;
    }

    /**
     * Find before greater index
     *
     * @param mixed $value
     * @return int
     */
    protected function findBeforeGreaterIndex($value): int {
        $begin = 0;
        $end = count($this->values) - 1;

        while ($begin < $end) {
            $middle = intdiv($begin + $end, 2);

            if ($this->values[$middle] <= $value) {
                $begin = $middle + 1;

                continue;
            }

            $end = $middle;
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
     * Insert primary key
     *
     * @param string $primaryKey
     * @param mixed $value
     * @return self
     */
    public function insert(string $primaryKey, $value) {
        $index = $this->findBeforeGreaterIndex($value);

        array_splice($this->primaryKeys, $index, 0, [$primaryKey]);
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
        // Key check
        if (array_keys($data) != [static::KEY_VALUES, static::KEY_PRIMARY_KEYS]) {
            return false;
        }

        // Value check
        $primaryKeys = $data[static::KEY_PRIMARY_KEYS];
        $values = $data[static::KEY_VALUES];

        if (!is_array($primaryKeys) || !is_array($values) || count($primaryKeys) !== count($values)) {
            return false;
        }

        // Load
        $this->primaryKeys = $primaryKeys;
        $this->values = $values;

        return true;
    }

    /**
     * Remove primary key
     *
     * @param string $primaryKey
     * @return self
     */
    public function remove(string $primaryKey) {
        if (false !== $index = array_search($primaryKey, $this->primaryKeys)) {
            array_splice($this->primaryKeys, $index, 1);
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
            static::KEY_PRIMARY_KEYS => $this->primaryKeys,
            static::KEY_VALUES => $this->values
        ];

        $this->fileSystem->put($this->filename, serialize($data));

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