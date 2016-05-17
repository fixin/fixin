<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\ResourceManager\ResourceManagerInterface;
use Fixin\Support\Ground;

class RuntimeCargoFactory implements FactoryInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ResourceManagerInterface $container, array $options = null, string $name = null) {
        $factory = new HttpCargoFactory();

        return $factory($container, $options, $name);
    }
}