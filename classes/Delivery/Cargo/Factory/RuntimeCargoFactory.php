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
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Ground;

class RuntimeCargoFactory implements FactoryInterface
{
    /**
     * Produce cargo
     *
     * @param ResourceManagerInterface $resourceManager
     * @param array|null $options
     * @return CargoInterface
     */
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null): CargoInterface
    {
        $factory = '*\Delivery\Cargo\Factory\\' . (Ground::isConsole() ? 'ConsoleCargoFactory' : 'HttpCargoFactory');

        return $resourceManager->clone($factory, CargoInterface::class);
    }
}
