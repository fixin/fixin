<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity\Cache;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface CacheInterface extends PrototypeInterface
{
    public const
        ENTITY_PROTOTYPE = 'entityPrototype',
        REPOSITORY = 'repository';

    /**
     * @return $this
     */
    public function clear(): CacheInterface;

    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface;

    /**
     * @return EntityInterface[]
     */
    public function getByIds(array $ids): array;

    /**
     * @return $this
     */
    public function invalidate(): CacheInterface;

    /**
     * @return $this
     */
    public function remove(EntityInterface $entity): CacheInterface;

    /**
     * @return $this
     */
    public function update(EntityInterface $entity): CacheInterface;
}
