<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityInterface extends PrototypeInterface {

    const
        OPTION_ENTITY_ID = 'entityId',
        OPTION_REPOSITORY = 'repository'
    ;

    /**
     * Delete from the repository
     *
     * @return self
     */
    public function delete(): EntityInterface;

    /**
     * @param array $data
     * @return self
     */
    public function exchangeArray(array $data): EntityInterface;

    /**
     * Get ID
     *
     * @return EntityIdInterface|null
     */
    public function getEntityId();

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Determine if deleted
     *
     * @return bool
     */
    public function isDeleted(): bool;

    /**
     * Determine if stored
     *
     * @return bool
     */
    public function isStored(): bool;

    /**
     * Save changes to the repository
     *
     * @return self
     */
    public function save(): EntityInterface;
}
