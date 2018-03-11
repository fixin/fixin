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
        ITEMS = 'items',
        ITERATOR = 'iterator',
        PREFETCH_SIZE = 'prefetchSize';

    /**
     * Get entity IDs
     *
     * @return EntityIdInterface[]
     */
    public function getEntityIds(): array;

    /**
     * Shuffle entities
     *
     * @return $this
     */
    public function shuffle(): EntitySetInterface;
}
