<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Index;

use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Arrays;

class SortedArrayIndex extends Prototype implements IndexInterface
{
    protected const
        EXCEPTION_FILENAME_NOT_SET = 'Filename not set',
        EXCEPTION_INVALID_DATA = 'Invalid data',
        KEY_KEYS = 'keys',
        KEY_VALUES = 'values',
        THIS_SETS_LAZY = [
            self::OPTION_FILE_SYSTEM => FileSystemInterface::class
        ];

    public const
        OPTION_FILE_SYSTEM = 'fileSystem',
        OPTION_FILENAME = 'filename';

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
    public function __destruct()
    {
        if (isset($this->filename)) {
            $this->flush();
        }
    }

    /**
     * @return static
     */
    public function clear(): IndexInterface
    {
        $this->keys = [];
        $this->values = [];

        $this->dirty = true;

        return $this;
    }

    /**
     * Find index for a value
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
     * @return static
     */
    public function flush(): IndexInterface
    {
        if ($this->dirty) {
            $this->save();
        }

        return $this;
    }

    protected function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem ?: $this->loadLazyProperty(static::OPTION_FILE_SYSTEM);
    }

    public function getKeysOf($value): array
    {
        return array_slice($this->keys, $start = $this->findIndex($value, -1), $this->findIndex($value, 0) - $start);
    }

    public function getKeysOfGreaterThan($value): array
    {
        return array_slice($this->keys, $this->findIndex($value, 0));
    }

    public function getKeysOfGreaterThanOrEqual($value): array
    {
        return array_slice($this->keys, $this->findIndex($value, -1));
    }

    public function getKeysOfInterval($beginValue, $endValue): array
    {
        return array_slice($this->keys, $start = $this->findIndex($beginValue, -1), $this->findIndex($endValue, 0, $start + 1) - $start);
    }

    public function getKeysOfLowerThan($value): array
    {
        return array_slice($this->keys, 0, $this->findIndex($value, -1));
    }

    public function getKeysOfLowerThanOrEqual($value): array
    {
        return array_slice($this->keys, 0, $this->findIndex($value, 0));
    }

    public function getKeysOfValues(array $values): array
    {
        return array_intersect_key($this->keys, array_intersect($this->values, $values));
    }

    public function getValue($key)
    {
        return (false !== $index = array_search($key, $this->keys)) ? $this->values[$index] : null;
    }

    public function getValues(array $keys): array
    {
        $filtered = array_intersect($this->keys, $keys);

        return array_combine($filtered, array_intersect_key($this->values, $filtered));
    }

    /**
     * Load data from the file
     *
     * @throws Exception\RuntimeException
     * @return static
     */
    protected function load(): self
    {
        if (!$this->filename) {
            throw new Exception\RuntimeException(static::EXCEPTION_FILENAME_NOT_SET);
        }

        $data = unserialize($this->getFileSystem()->getFileContents($this->filename), ['allowed_classes' => false]);
        if (!is_array($data) || !$this->loadArray($data)) {
            throw new Exception\RuntimeException(static::EXCEPTION_INVALID_DATA);
        }

        $this->dirty = false;

        return $this;
    }

    /**
     * Load data from array
     */
    protected function loadArray(array $data): bool
    {
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
     * @return static
     */
    public function rollback(): IndexInterface
    {
        $this->load();

        return $this;
    }

    /**
     * Save data
     *
     * @throws Exception\RuntimeException
     * @return static
     */
    protected function save(): self
    {
        if (!$this->filename) {
            throw new Exception\RuntimeException(static::EXCEPTION_FILENAME_NOT_SET);
        }

        $data = [
            static::KEY_KEYS => $this->keys,
            static::KEY_VALUES => $this->values
        ];

        $this->getFileSystem()->putFileContentsWithLock($this->filename, serialize($data));

        $this->dirty = false;

        return $this;
    }

    /**
     * @return static
     */
    public function set($key, $value): IndexInterface
    {
        $this->unset($key);

        $index = $this->findIndex($value, -1);

        array_splice($this->keys, $index, 0, [$key]);
        array_splice($this->values, $index, 0, [$value]);

        $this->dirty = true;

        return $this;
    }

    protected function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return static
     */
    public function unset($key): IndexInterface
    {
        if (false !== $index = array_search($key, $this->keys)) {
            array_splice($this->keys, $index, 1);
            array_splice($this->values, $index, 1);

            $this->dirty = true;
        }

        return $this;
    }
}
