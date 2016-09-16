<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\Prototype;

class EntityCache extends Prototype implements EntityCacheInterface {

    const
        THIS_REQUIRES = [
            self::OPTION_ENTITY_PROTOTYPE => self::TYPE_INSTANCE,
            self::OPTION_REPOSITORY => self::TYPE_INSTANCE,
        ]
    ;

    /**
     * @var array
     */
    protected $entities = [];

    /**
     * @var EntityInterface
     */
    protected $entityPrototype;

    /**
     * @var array
     */
    protected $invalidEntities = [];

    /**
     * @var array
     */
    protected $primaryKeyFlipped;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityCacheInterface::fetchResultEntity($storageResult)
     */
    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface {
        // Fetch
        $data = $storageResult->current();
        $storageResult->next();

        // Id
        $id = $this->repository->createId(array_intersect_key($data, $this->primaryKeyFlipped));
        $key = (string) $id;

        // Invalid entity (needs update)
        if (isset($this->invalidEntities[$key])) {
            $entity = $this->invalidEntities[$key];
            unset($this->invalidEntities[$key]);

            return $this->entities[$key] = $entity->exchangeArray($data);
        }

        // Active entity
        if (isset($this->entities[$key])) {
            return $this->entities[$key];
        }

        return $this->entities[$key] = $this->entityPrototype->withOptions([
            EntityInterface::OPTION_ENTITY_ID => $id
        ])->exchangeArray($data);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityCacheInterface::getByIds($ids)
     */
    public function getByIds(array $ids): array {
        if ($ids) {
            $ids = array_combine($ids, $ids);
            $list = array_fill_keys(array_keys($ids), null);

            // Cached entities
            $cached = array_intersect_key($this->entities, $list);
            $list = array_replace($list, $cached);

            // Fetch required data
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

        return [];
    }

    /**
     * Set entity prototype
     *
     * @param EntityInterface $entityPrototype
     */
    protected function setEntityPrototype(EntityInterface $entityPrototype) {
        $this->entityPrototype = $entityPrototype;
    }

    /**
     * Set repository
     *
     * @param RepositoryInterface $repository
     */
    protected function setRepository(RepositoryInterface $repository) {
        $this->repository = $repository;
        $this->primaryKeyFlipped = array_flip($repository->getPrimaryKey());
    }
}