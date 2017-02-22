<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Factory\Factory;
use Fixin\Support\Ground;

class RuntimeCargoFactory extends Factory
{
    public function __invoke(array $options = null, string $name = null): CargoInterface
    {
        $factory = 'Delivery\Cargo\Factory\\' . (Ground::isConsole() ? 'ConsoleCargoFactory' : 'HttpCargoFactory');

        return $this->container->clonePrototype($factory);
    }
}
