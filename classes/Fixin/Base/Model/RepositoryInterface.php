<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model;

use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    /**
     * Get single entity
     *
     * @param int|string|array $id
     * @return EntityInterface|null
     */
    public function get($id);

    /*
    public function create();
    public function delete();
    public function getName();
    public function save();
    public function select();*/
}