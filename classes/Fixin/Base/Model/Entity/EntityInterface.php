<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model\Entity;

use Fixin\Base\Model\Repository\RepositoryInterface;
use Fixin\Resource\ResourceInterface;
use Fixin\Support\PrototypeInterface;

interface EntityInterface extends ResourceInterface, PrototypeInterface {

    /**
     * Delete entity from the repository
     *
     * @return self
     */
    public function delete();

    /**
     * Save entity to the repository
     *
     * @return self
     */
    public function save();

    /**
     * New instance for repository
     *
     * @param RepositoryInterface $repository
     * @return EntityInterface
     */
    public function withRepository(RepositoryInterface $repository): EntityInterface;
}