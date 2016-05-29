<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage;

use Fixin\Base\Model\Entity\EntityInterface;
use Fixin\Base\Model\Repository\RepositoryInterface;
use Fixin\Resource\ResourceInterface;

interface StorageInterface extends ResourceInterface {

    public function get(RepositoryInterface $repository, $id): EntityInterface;
}