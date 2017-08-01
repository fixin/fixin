<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntitySetInterface extends PrototypeInterface, \Iterator, \Countable
{
    public const
        ENTITY_CACHE = 'entityCache',
        ID_FETCH_MODE = 'idFetchMode',
        ITEMS = 'items',
        PREFETCH_SIZE = 'prefetchSize',
        REPOSITORY = 'repository',
        STORAGE_RESULT = 'storageResult';

    /**
     * @return EntityIdInterface[]
     */
    public function getEntityIds(): array;

    public function getRepository(): RepositoryInterface;

    /**
     * @return $this
     */
    public function shuffle(): EntitySetInterface;
}
