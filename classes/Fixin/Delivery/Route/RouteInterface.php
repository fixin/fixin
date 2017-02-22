<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Route;

use Fixin\Delivery\Cargo\CargoHandlerInterface;

interface RouteInterface extends CargoHandlerInterface
{
    public const
        OPTION_NODES = 'nodes';
}
