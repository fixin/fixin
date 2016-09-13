<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Storage\StorageResultInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityCacheInterface extends PrototypeInterface {

    const OPTION_REPOSITORY = 'repository';

    public function fetchResultEntity(StorageResultInterface $storageResult): EntityInterface;

    public function getByIds(array $ids): array;
}