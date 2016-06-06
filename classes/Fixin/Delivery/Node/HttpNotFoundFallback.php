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

class HttpNotFoundFallback extends HttpHub {

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Node\HttpHub::handleHttpCargo($cargo)
     */
    protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface {
        return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
    }
}