<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\ResourceInterface;

interface CargoHandlerInterface extends ResourceInterface
{
    /**
     * Handle
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function handle(CargoInterface $cargo): CargoInterface;
}
