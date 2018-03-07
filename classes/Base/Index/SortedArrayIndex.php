<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Index;

use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Arrays;
use Fixin\Support\Types;

class SortedArrayIndex extends Prototype implements IndexInterface
{
    public const
        FILE_SYSTEM = 'fileSystem',
        FILENAME = 'filename';

    protected const
        MISSING_FILENAME_EXCEPTION = 'Filename not set',
        INVALID_DATA_EXCEPTION = 'Invalid data',
        KEYS_KEY = 'keys',
        THIS_SETS = [
            self::FILE_SYSTEM => [self::LAZY_LOADING => FileSystemInterface::class, Types::NULL],
            self::FILENAME => [Types::STRING, Types::NULL]
        ],
        VALUES_KEY = 'values';

    /**
     * @var bool
     */
    protected $dirty = false;

    /**
     * @var FileSystemInterface|false|null
     */
    protected $fileSystem;

    /**
     * @var string|null
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
    public function __destruct()
    {
        if (isset($this->filename)) {
            $this->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function clear(): IndexInterface
    {
        $this->keys = [];
        $this->values = [];

        $this->dirty = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete($key): IndexInterface
    {
        if (false !== $index = array_search($key, $this->keys)) {
            array_splice($this->keys, $index, 1);
            array_splice($this->values, $index, 1);

            $this->dirty = true;
        }

        return $this;
    }

    /**
     * Find index for a value
     *
     * @param $value
     * @param int $compareLimit
     * @param int $begin
     * @return int
     */
    protected function findIndex($value, int $compareLimit, int $begin = 0): int
    {
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
     * Get file system
     *
     * @return FileSystemInterface
     */
    protected function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem ?: $this->loadLazyProperty(static::FILE_SYSTEM);
    }

    /**
     * @inheritDoc
     */
    public function getKeysOf($value): array
    {
        return array_slice($this->keys, $start = $this->findIndex($value, -1), $this->findIndex($value, 0) - $start);
    }

    /**
     * @inheritDoc
     */
    public function getKeysOfGreaterThan($value): array
    {
        return array_slice($this->keys, $this->findIndex($value, 0));
    }

    /**
     * @inheritDoc
     */
    public function getKeysOfGreaterThanOrEqual($value): array
    {
        return array_slice($this->keys, $this->findIndex($value, -1));
    }

    /**
     * @inheritDoc
     */
    public function getKeysOfInterval($beginValue, $endValue): array
    {
        return array_slice($this->keys, $start = $this->findIndex($beginValue, -1), $this->findIndex($endValue, 0, $start + 1) - $start);
    }

    /**
     * @inheritDoc
     */
    public function getKeysOfLowerThan($value): array
    {
        return array_slice($this->keys, 0, $this->findIndex($value, -1));
    }

    /**
     * @inheritDoc
     */
    public function getKeysOfLowerThanOrEqual($value): array
    {
        return array_slice($this->keys, 0, $this->findIndex($value, 0));
    }

    /**
     * @inheritDoc
     */
    public function getKeysOfValues(array $values): array
    {
        return array_intersect_key($this->keys, array_intersect($this->values, $values));
    }

    /**
     * @inheritDoc
     */
    public function getValue($key)
    {
        return (false !== $index = array_search($key, $this->keys)) ? $this->values[$index] : null;
    }

    /**
     * @inheritDoc
     */
    public function getValues(array $keys): array
    {
        $filtered = array_intersect($this->keys, $keys);

        return array_combine($filtered, array_intersect_key($this->values, $filtered));
    }

    /**
     * Load data from the file
     *
     * @return $this
     */
    protected function load(): self
    {
        if (is_null($this->filename)) {
            throw new Exception\RuntimeException(static::MISSING_FILENAME_EXCEPTION);
        }

        $data = unserialize($this->getFileSystem()->getFileContents($this->filename), ['allowed_classes' => false]);
        if (!is_array($data) || !$this->loadArray($data)) {
            throw new Exception\InvalidDataException(static::INVALID_DATA_EXCEPTION);
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
    protected function loadArray(array $data): bool
    {
        // Value check
        $keys = Arrays::getArrayForKey($data, static::KEYS_KEY);
        $values = Arrays::getArrayForKey($data, static::VALUES_KEY);

        if (is_null($keys) || is_null($values) || count($keys) !== count($values)) {
            return false;
        }

        // Load
        $this->keys = $keys;
        $this->values = $values;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function revert(): IndexInterface
    {
        $this->load();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(): IndexInterface
    {
        if ($this->dirty) {
            $this->saveProcess();
        }

        return $this;
    }

    /**
     * Save data
     *
     * @return $this
     */
    protected function saveProcess(): self
    {
        if (is_null($this->filename)) {
            throw new Exception\RuntimeException(static::MISSING_FILENAME_EXCEPTION);
        }

        $data = [
            static::KEYS_KEY => $this->keys,
            static::VALUES_KEY => $this->values
        ];

        $this->getFileSystem()->putFileContentsWithLock($this->filename, serialize($data));

        $this->dirty = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value): IndexInterface
    {
        $this->delete($key);

        $index = $this->findIndex($value, -1);

        array_splice($this->keys, $index, 0, [$key]);
        array_splice($this->values, $index, 0, [$value]);

        $this->dirty = true;

        return $this;
    }
}
