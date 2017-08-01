<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Route;

use Fixin\Delivery\Cargo\CargoHandlerInterface;

interface RouteInterface extends CargoHandlerInterface
{
    public const
        NODES = 'nodes';
}
