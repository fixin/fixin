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

interface EntityIdInterface extends PrototypeInterface
{
    public const
        ENTITY_ID = 'entityId',
        REPOSITORY = 'repository';

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Delete entity
     *
     * @return bool
     */
    public function deleteEntity(): bool;

    /**
     * Get array copy
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Get entity
     *
     * @return EntityInterface|null
     */
    public function getEntity(): ?EntityInterface;

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;
}
