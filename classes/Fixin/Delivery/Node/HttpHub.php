<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Http;

abstract class HttpHub extends Resource implements NodeInterface
{
    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo instanceof HttpCargoInterface && $cargo->getStatusCode() === Http::STATUS_CONTINUE_100) {
            return $this->handleHttpCargo($cargo);
        }

        return $cargo;
    }

    abstract protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface;
}
