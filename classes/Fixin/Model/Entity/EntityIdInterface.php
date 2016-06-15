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
     * Get repository of ID
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;
}