<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model;

use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    const OPTION_NAME = 'name';
    const OPTION_PRIMARY_KEY = 'primaryKey';
    const OPTION_STORAGE = 'storage';

    /**
     * Get single entity
     *
     * @param int|string|array $key
     * @return EntityInterface|null
     */
    public function get($key);
}