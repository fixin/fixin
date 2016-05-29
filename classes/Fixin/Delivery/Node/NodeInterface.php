<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\ResourceInterface;

interface NodeInterface extends ResourceInterface {

    /**
     * Handle cargo
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function handle(CargoInterface $cargo);
}