<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity;

use Fixin\Resource\PrototypeInterface;

interface EntityInterface extends PrototypeInterface
{
    public const
        ENTITY_ID = 'entityId',
        REPOSITORY = 'repository';

    /**
     * Collect save data
     *
     * @return array
     */
    public function collectSaveData(): array;

    /**
     * Delete from the repository
     *
     * @return $this
     */
    public function delete(): EntityInterface;

    /**
     * Exchange array data
     *
     * @param array $data
     * @return $this
     */
    public function exchangeArray(array $data): EntityInterface;

    /**
     * Get entity ID
     *
     * @return EntityIdInterface|null
     */
    public function getEntityId(): ?EntityIdInterface;

    /**
     * Determine if stored
     *
     * @return bool
     */
    public function isStored(): bool;

    /**
     * Refresh from repository
     *
     * @return $this
     */
    public function refresh(): EntityInterface;

    /**
     * Save changes to the repository
     *
     * @return $this
     */
    public function save(): EntityInterface;
}
