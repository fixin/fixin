<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity\Cache;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Storage\StorageResultInterface;

class RuntimeCache extends Cache
{
    /**
     * @var EntityInterface[]
     */
    protected $entities = [];

    /**
     * @var EntityInterface[]
     */
    protected $invalidEntities = [];

    /**
     * @return static
     */
    public function clear(): CacheInterface
    {
        $this->entities = [];
        $this->invalidEntities = [];

        return $this;
    }

    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface
    {
        $data = $storageResult->current();
        $storageResult->next();

        $entityId = $this->repository->createId(array_intersect_key($data, $this->primaryKeyFlipped));
        $key = (string) $entityId;

        if (isset($this->entities[$key])) {
            return $this->entities[$key];
        }

        if (isset($this->invalidEntities[$key])) {
            $entity = $this->invalidEntities[$key];
            unset($this->invalidEntities[$key]);

            return $this->entities[$key] = $entity->exchangeArray($data);
        }

        return $this->entities[$key] = $this->entityPrototype->withOptions([
            EntityInterface::OPTION_ENTITY_ID => $entityId
        ])->exchangeArray($data);
    }

    public function getByIds(array $ids): array
    {
        $ids = array_combine($ids, $ids);
        $cached = array_intersect_key($this->entities, $ids);
        $list = array_replace(array_fill_keys(array_keys($ids), null), $cached);

        if ($ids = array_diff_key($ids, $cached)) {
            $request = $this->repository->createRequest();
            $request->getWhere()->in($this->repository->getPrimaryKey(), $ids);

            $storageResult = $this->repository->selectRawData($request);
            while ($storageResult->valid()) {
                $entity = $this->fetchResultEntity($storageResult);
                $list[(string) $entity->getEntityId()] = $entity;
            }
        }

        return array_filter($list);
    }

    /**
     * @return static
     */
    public function invalidate(): CacheInterface
    {
        $this->invalidEntities = $this->entities + $this->invalidEntities;
        $this->entities = [];

        return $this;
    }

    /**
     * @return static
     */
    public function remove(EntityInterface $entity): CacheInterface
    {
        $key = (string) $entity->getEntityId();

        unset($this->entities[$key], $this->invalidEntities[$key]);

        return $this;
    }

    /**
     * @return static
     */
    public function update(EntityInterface $entity): CacheInterface
    {
        $key = (string) $entity->getEntityId();

        unset($this->invalidEntities[$key]);
        $this->entities[$key] = $entity;

        return $this;
    }
}
