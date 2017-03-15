<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity\Cache;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface CacheInterface extends PrototypeInterface
{
    public const
        OPTION_ENTITY_PROTOTYPE = 'entityPrototype',
        OPTION_REPOSITORY = 'repository';

    public function clear(): CacheInterface;
    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface;

    /**
     * @return EntityInterface[]
     */
    public function getByIds(array $ids): array;

    public function invalidate(): CacheInterface;
    public function remove(EntityInterface $entity): CacheInterface;
    public function update(EntityInterface $entity): CacheInterface;
}
