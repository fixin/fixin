<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Resource\Prototype;
use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Support\Arrays;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Index extends Prototype {

    const EXCEPTION_FILENAME_NOT_SET = 'Filename not set';
    const EXCEPTION_INVALID_DATA = 'Invalid data';

    const KEY_ENTITY_IDS = 'entityIds';
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
    protected $entityIds = [];

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
        $this->entityIds = [];
        $this->values = [];

        $this->dirty = true;

        return $this;
    }

    /**
     * IDs of equal values
     *
     * @param mixed $value
     * @return array
     */
    public function equal($value): array {
        return array_slice($this->entityIds, $start = $this->findIndex($value, -1), $this->findIndex($value, 0) - $start);
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
     * Get values for IDs
     *
     * @param array $entityIds
     * @return array
     */
    public function getValuesForIds(array $entityIds): array {
        $filtered = array_intersect($this->entityIds, $entityIds);

        return array_combine($filtered, array_intersect_key($this->values, $filtered));
    }

    /**
     * IDs of greather than values
     *
     * @param mixed $value
     * @return array
     */
    public function greaterThan($value): array {
        return array_slice($this->entityIds, $this->findIndex($value, 0));
    }

    /**
     * IDs of greather than or equal values
     *
     * @param mixed $value
     * @return array
     */
    public function greaterThanOrEqual($value): array {
        return array_slice($this->entityIds, $this->findIndex($value, -1));
    }

    /**
     * IDs of values
     *
     * @param array $values
     * @return array
     */
    public function inValues(array $values): array {
        return array_intersect_key($this->entityIds, array_intersect($this->values, $values));
    }

    /**
     * Insert value for ID
     *
     * @param mixed $entityId
     * @param mixed $value
     * @return self
     */
    public function insert($entityId, $value) {
        $index = $this->findIndex($value, -1);

        array_splice($this->entityIds, $index, 0, [$entityId]);
        array_splice($this->values, $index, 0, [$value]);

        $this->dirty = true;

        return $this;
    }

    /**
     * IDs of values of interval
     *
     * @param mixed $beginValue
     * @param mixed $endValue
     * @return array
     */
    public function intervalOf($beginValue, $endValue): array {
        return array_slice($this->entityIds, $start = $this->findIndex($beginValue, -1), $this->findIndex($endValue, 0) - $start);
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
        $entityIds = Arrays::arrayForKey($data, static::KEY_ENTITY_IDS);
        $values = Arrays::arrayForKey($data, static::KEY_VALUES);

        if (is_null($entityIds) || is_null($values) || count($entityIds) !== count($values)) {
            return false;
        }

        // Load
        $this->entityIds = $entityIds;
        $this->values = $values;

        return true;
    }

    /**
     * IDs of lower than values
     *
     * @param mixed $value
     * @return array
     */
    public function lowerThan($value): array {
        return array_slice($this->entityIds, 0, $this->findIndex($value, -1));
    }

    /**
     * IDs of lower than or equal values
     *
     * @param mixed $value
     * @return array
     */
    public function lowerThanOrEqual($value): array {
        return array_slice($this->entityIds, 0, $this->findIndex($value, 0));
    }

    /**
     * Remove ID
     *
     * @param mixed $entityId
     * @return self
     */
    public function remove($entityId) {
        if (false !== $index = array_search($entityId, $this->entityIds)) {
            array_splice($this->entityIds, $index, 1);
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
            static::KEY_ENTITY_IDS => $this->entityIds,
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