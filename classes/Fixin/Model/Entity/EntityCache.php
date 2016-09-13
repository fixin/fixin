<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Resource\Prototype;
use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Model\Repository\RepositoryInterface;

class EntityCache extends Prototype implements EntityCacheInterface {

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface {
		// TODO
    }

    public function getByIds(array $ids): array {
		// TODO
    }

    /**
     * Set repository
     *
     * @param RepositoryInterface $repository
     */
    protected function setRepository(RepositoryInterface $repository) {
        $this->repository = $repository;
    }
}