<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model\Repository;

use Fixin\Base\Model\Entity\EntityInterface;
use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    /**
     * Get single entity
     *
     * @param int|string|array $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /*
    public function create();
    public function delete();
    public function getName();
    public function save();
    public function select();*/
}