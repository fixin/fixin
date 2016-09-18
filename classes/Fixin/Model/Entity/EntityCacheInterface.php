<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityCacheInterface extends PrototypeInterface {

    const
    OPTION_ENTITY_PROTOTYPE = 'entityPrototype',
    OPTION_REPOSITORY = 'repository';

    /**
     * Clear
     *
     * @return EntityCacheInterface
     */
    public function clear(): EntityCacheInterface;

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
     * @return EntityInterface
     */
    public function invalidate(): EntityInterface;
}