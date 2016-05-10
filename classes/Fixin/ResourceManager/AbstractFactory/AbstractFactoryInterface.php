<?php

namespace Fixin\ResourceManager\AbstractFactory;

use Fixin\Support\ContainerInterface;

interface AbstractFactoryInterface {

    /**
     * Determines if we can produce a resource by name
     *
     * @param ContainerInterface $container
     * @param string $name
     * @return bool
     */
    public function canProduce(ContainerInterface $container, string $name): bool;

    /**
     * Produces resource
     *
     * @param ContainerInterface $container
     * @param string $name
     */
    public function produce(ContainerInterface $container, string $name);
}