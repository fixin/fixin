<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity\Cache;

use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

abstract class Cache extends Prototype implements CacheInterface {

    const
    THIS_REQUIRES = [
        self::OPTION_ENTITY_PROTOTYPE => self::TYPE_INSTANCE,
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE,
    ];

    /**
     * @var EntityInterface
     */
    protected $entityPrototype;

    /**
     * @var array
     */
    protected $primaryKeyFlipped;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

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