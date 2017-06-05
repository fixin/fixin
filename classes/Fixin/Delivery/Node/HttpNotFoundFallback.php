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

class HttpNotFoundFallback extends AbstractHttpHub
{
    protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface
    {
        return $this->replyNotFound($cargo);
    }
}
