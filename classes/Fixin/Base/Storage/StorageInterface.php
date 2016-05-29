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
     * @param RepositoryInterface $repository
     * @param string $id
     */
    public function get(RepositoryInterface $repository, $id);
}