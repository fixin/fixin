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
     * @param int|string|array $key
     * @return EntityInterface|null
     */
    public function get($key);
}