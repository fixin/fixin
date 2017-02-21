<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityIdInterface extends PrototypeInterface
{
    public const
        OPTION_ENTITY_ID = 'entityId',
        OPTION_REPOSITORY = 'repository';

    public function __toString(): string;
    public function deleteEntity(): bool;
    public function getArrayCopy(): array;

    /**
     * Get the entity for the ID
     */
    public function getEntity(): ?EntityInterface;

    public function getRepository(): RepositoryInterface;
}
