<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Resource\PrototypeInterface;
use Fixin\Model\Entity\EntityInterface;

interface RepositoryRequestInterface extends PrototypeInterface {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Delete record(s)
     *
     * @return int
     */
    public function delete(): int;

    /**
     * First record
     *
     * @return EntityInterface
     */
    public function first(): EntityInterface;

    /**
     * Get entities
     *
     * @return EntitySetInterface
     */
    public function get(): EntitySetInterface;

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Update record(s)
     *
     * @param array $set
     * @return int
     */
    public function update(array $set): int;
}