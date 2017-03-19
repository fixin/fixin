<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Ground;

class RuntimeCargoFactory extends Resource implements FactoryInterface
{
    public function __invoke(array $options = null, string $name = null): CargoInterface
    {
        $factory = 'Delivery\Cargo\Factory\\' . (Ground::isConsole() ? 'ConsoleCargoFactory' : 'HttpCargoFactory');

        return $this->container->clone($factory);
    }
}
