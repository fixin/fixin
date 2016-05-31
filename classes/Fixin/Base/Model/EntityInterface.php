<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model;

use Fixin\Resource\PrototypeInterface;

interface EntityInterface extends PrototypeInterface {

    /**
     * Delete entity from the repository
     *
     * @return self
     */
    public function delete(): EntityInterface;

    /**
     * Save entity to the repository
     *
     * @return self
     */
    public function save(): EntityInterface;
}