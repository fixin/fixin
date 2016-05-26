<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\ResourceManager\Factory\Factory;
use Fixin\Support\Ground;

class RuntimeCargoFactory extends Factory {

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __invoke(array $options = null, string $name = null) {
        $factory = 'Delivery\Cargo\Factory\\' . (Ground::isConsole() ? 'ConsoleCargoFactory' : 'HttpCargoFactory');

        return $this->container->clonePrototype($factory);
    }
}