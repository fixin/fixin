<?php

namespace Fixin\ResourceManager\Factory;

use Fixin\Support\ContainerInterface;

interface FactoryInterface {

    /**
     * Produces resource
     *
     * @param ContainerInterface $container
     * @param string $name
     */
    public function __invoke(ContainerInterface $container, string $name = null);
}