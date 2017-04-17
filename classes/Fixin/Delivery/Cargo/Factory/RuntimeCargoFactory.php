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
use Fixin\Resource\Factory;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Ground;

class RuntimeCargoFactory implements FactoryInterface
{
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null, string $name = null): CargoInterface
    {
        $factory = 'Delivery\Cargo\Factory\\' . (Ground::isConsole() ? 'ConsoleCargoFactory' : 'HttpCargoFactory');

        return $resourceManager->clone($factory, CargoInterface::class);
    }
}
