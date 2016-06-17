<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Resource\PrototypeInterface;

interface RepositoryRequestInterface extends PrototypeInterface {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Delete record(s)
     *
     * @return int
     */
    public function delete(): int;

    /**
     * Get entities
     *
     * @return EntitySetInterface
     */
    public function get(): EntitySetInterface;

    /**
     * Update record(s)
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;
}