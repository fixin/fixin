<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityIdInterface extends PrototypeInterface {

    const OPTION_ENTITY_ID = 'entityId';
    const OPTION_REPOSITORY = 'repository';

    /**
     * Delete entity of ID
     *
     * @return EntityIdInterface
     */
    public function deleteEntity(): self;

    /**
     * Fetch entity of ID
     *
     * @return EntityInterface|null
     */
    public function fetchEntity();

    /**
     * Get repository of ID
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;
}