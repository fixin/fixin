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

    public function __toString(): string;
    public function deleteEntity(): bool;
    public function getArrayCopy(): array;

    /**
     * Get the entity for the ID
     */
    public function getEntity(): ?EntityInterface;

    public function getRepository(): RepositoryInterface;
}
