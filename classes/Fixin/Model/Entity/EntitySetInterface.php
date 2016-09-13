<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Resource\PrototypeInterface;
use Fixin\Model\Repository\RepositoryInterface;

interface EntitySetInterface extends PrototypeInterface, \Iterator, \Countable {

    const
        OPTION_ID_FETCH = 'idFetch',
        OPTION_PREFETCH_SIZE = 'prefetchSize',
        OPTION_REPOSITORY = 'repository',
        OPTION_STORAGE_RESULT = 'storageResult'
    ;

    /**
     * Get entity IDs of all items
     *
     * @return EntityIdInterface[]
     */
    public function getEntityIds(): array;

    /**
     * Get prefetch size
     *
     * @return int
     */
    public function getPrefetchSize(): int;

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Prefetch all entities
     *
     * @return self
     */
    public function prefetchAll(): self;

    /**
     * Set count of entities for prefetch
     *
     * @param int $prefetchSize
     * @return self
     */
    public function setPrefetchSize(int $prefetchSize): self;

    /**
     * Shuffle entities
     *
     * @return self
     */
    public function shuffle(): self;
}