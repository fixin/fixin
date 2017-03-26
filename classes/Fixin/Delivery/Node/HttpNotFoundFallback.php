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
use Fixin\Support\Http;

class HttpNotFoundFallback extends HttpHub
{
    protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface
    {
        return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
    }
}
