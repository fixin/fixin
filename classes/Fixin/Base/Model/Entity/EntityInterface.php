<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model\Entity;

use Fixin\Base\Model\Repository\RepositoryInterface;
use Fixin\ResourceManager\ResourceInterface;
use Fixin\Support\PrototypeInterface;

interface EntityInterface extends ResourceInterface, PrototypeInterface {

    /**
     *
     */
    public function delete();

    /**
     *
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