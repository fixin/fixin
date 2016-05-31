<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage;

use Fixin\Base\Model\RepositoryInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface {

    /**
     * Get data
     *
     * @param RepositoryInterface $repository
     * @param string $key
     * @return mixed
     */
    public function get(RepositoryInterface $repository, $key);
}