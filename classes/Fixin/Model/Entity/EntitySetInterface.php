<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntitySetInterface extends PrototypeInterface, \Iterator, \Countable {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Append another entity set
     *
     * @param self $entitySet
     * @return self
     */
    public function append(self $entitySet): self;

    /**
     * Get column values
     *
     * @param string $name
     * @return array
     */
    public function getColumn(string $name): array;

    /**
     * Get entity IDs of all items
     *
     * @return EntityIdInterface[]
     */
    public function getEntityIds(): array;

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