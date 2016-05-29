<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model\Repository;

use Fixin\Base\Model\Entity\EntityInterface;
use Fixin\Resource\Resource;

class Repository extends Resource implements RepositoryInterface {

    /**
     * @var string[]
     */
    protected $primaryKey = ['id'];

    /**
     * @var EntityInterface|string
     */
    protected $prototypeEntity = '\Fixin\Base\Model\Entity';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Model\RepositoryInterface::get($id)
     */
    public function get($id): EntityInterface {
        $data = $this->storage->get($this, $id);
        return $data;
    }
}