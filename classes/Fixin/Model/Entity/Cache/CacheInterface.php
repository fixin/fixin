<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity\Cache;

use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;
use Fixin\Model\Entity\EntityInterface;

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
     * @return EntityInterface
     */
    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface;

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

    /**
     * Remove entity
     *
     * @param EntityInterface $entity
     * @return CacheInterface
     */
    public function remove(EntityInterface $entity): CacheInterface;

    /**
     * Update entity
     *
     * @param EntityInterface $entity
     * @return CacheInterface
     */
    public function update(EntityInterface $entity): CacheInterface;
}