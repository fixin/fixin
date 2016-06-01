<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\ResourceInterface;

interface CargoHandlerInterface extends ResourceInterface {

    /**
     * Handle cargo
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function handle(CargoInterface $cargo): CargoInterface;
}