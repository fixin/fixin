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
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;

abstract class Cache extends Prototype implements CacheInterface
{
    protected const
        THIS_SETS = [
            self::ENTITY_PROTOTYPE => EntityInterface::class,
            self::REPOSITORY => self::USING_SETTER
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

    protected function setRepository(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
        $this->primaryKeyFlipped = array_flip($repository->getPrimaryKey());
    }
}
