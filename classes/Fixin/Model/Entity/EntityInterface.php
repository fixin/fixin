<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityInterface extends PrototypeInterface
{
    public const
        OPTION_ENTITY_ID = 'entityId',
        OPTION_REPOSITORY = 'repository';

    public function collectSaveData(): array;

    /**
     * Delete from the repository
     */
    public function delete(): EntityInterface;

    /**
     * Exchange array data
     */
    public function exchangeArray(array $data): EntityInterface;

    public function getEntityId(): ?EntityIdInterface;
    public function getRepository(): RepositoryInterface;
    public function isStored(): bool;

    /**
     * Refresh from repository
     */
    public function refresh(): EntityInterface;

    /**
     * Save changes to the repository
     */
    public function save(): EntityInterface;
}
