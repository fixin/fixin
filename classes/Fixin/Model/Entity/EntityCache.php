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
    protected $invalidatedEntities = [];

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
        $data = $storageResult->current();
        $storageResult->next();

        // TODO: cache test

        $entity = $this->entityPrototype->withOptions([
            EntityInterface::OPTION_ENTITY_ID => $this->repository->createId(array_intersect_key($data, $this->primaryKeyFlipped))
        ]);

        return $entity;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityCacheInterface::getByIds($ids)
     */
    public function getByIds(array $ids): array {
        if ($ids) {
            $list = array_fill_keys(array_map(function($item) { return $item; }, $ids), null);

            // TODO: cache test

            // Fetch data
            if ($ids) {
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