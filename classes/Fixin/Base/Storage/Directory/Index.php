<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\ResourceManager\Resource;
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
     * @var bool
     */
    protected $prepared = false;

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
     * @return \Fixin\Base\Storage\Directory\Index
     */
    public function clear() {
        $this->primaryKeys = [];
        $this->values = [];

        return $this;
    }

    /**
     * Write data if dirty
     *
     * @return \Fixin\Base\Storage\Directory\Index
     */
    public function flush() {
        if ($this->dirty) {
            $this->save();

            $this->dirty = false;
        }

        return $this;
    }

    /**
     * Insert primary key
     *
     * @param string $primaryKey
     * @param mixed $value
     * @return \Fixin\Base\Storage\Directory\Index
     */
    public function insert(string $primaryKey, $value) {
        $index = $this->findBefore($value);

        array_splice($this->primaryKeys, $index, 0, [$primaryKey]);
        array_splice($this->values, $index, 0, [$value]);
        $this->dirty = true;

        return $this;
    }

    /**
     * Load data
     *
     * @throws RuntimeException
     * @return \Fixin\Base\Storage\Directory\Index
     */
    protected function load() {
        if (!$this->filename) {
            throw new RuntimeException(static::EXCEPTION_FILENAME_NOT_SET);
        }

        $data = unserialize($this->fileSystem->get($this->filename), ['allowed_classes' => false]);
        if (!isset($data[static::KEY_VALUES]) || !isset($data[static::KEY_PRIMARY_KEYS])) {
            throw new RuntimeException(static::EXCEPTION_INVALID_DATA);
        }

        return $this;
    }

    /**
     * Remove primary key
     *
     * @param string $primaryKey
     * @return \Fixin\Base\Storage\Directory\Index
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
     * Save data
     *
     * @throws RuntimeException
     * @return \Fixin\Base\Storage\Directory\Index
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