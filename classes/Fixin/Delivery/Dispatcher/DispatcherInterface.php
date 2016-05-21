<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Dispatcher;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Support\PrototypeInterface;

interface DispatcherInterface extends PrototypeInterface {

    /**
     * Dispatch cargo
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function dispatch(CargoInterface $cargo);
}