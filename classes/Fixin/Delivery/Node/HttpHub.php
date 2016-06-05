<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Support\Http;

abstract class HttpHub extends Node {

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if ($cargo instanceof HttpCargoInterface && $cargo->getStatusCode() === Http::STATUS_CONTINUE_100) {
            return $this->handleHttpCargo($cargo);
        }

        return $cargo;
    }

    /**
     * Handle HttpCargoInterface instance
     *
     * @param HttpCargoInterface $cargo
     * @return CargoInterface
     */
    abstract protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface;
}