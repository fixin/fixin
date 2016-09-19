<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity\Cache;

use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface CacheInterface extends PrototypeInterface {

    const
    OPTION_ENTITY_PROTOTYPE = 'entityPrototype',
    OPTION_REPOSITORY = 'repository';

    /**
     * Clear
     *
     * @return CacheInterface
     */
    public function clear(): CacheInterface;

    /**
     * Fetch result entity
     *
     * @param StorageResultInterface $storageResult
     * @return CacheInterface
     */
    public function fetchResultEntity(StorageResultInterface $storageResult): CacheInterface;

    /**
     * Get entities by IDs
     *
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids): array;

    /**
     * Invalidate entities
     *
     * @return CacheInterface
     */
    public function invalidate(): CacheInterface;
}