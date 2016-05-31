<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Route;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\ResourceInterface;

interface RouteInterface extends ResourceInterface {

    const OPTION_NODES = 'nodes';

    /**
     * Dispatch cargo
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function dispatch(CargoInterface $cargo): CargoInterface;
}