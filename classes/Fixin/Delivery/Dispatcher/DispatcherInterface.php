<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Dispatcher;

use Fixin\Delivery\Cargo\CargoInterface;

interface DispatcherInterface {

    /**
     * Dispatch cargo
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function dispatch(CargoInterface $cargo);
}