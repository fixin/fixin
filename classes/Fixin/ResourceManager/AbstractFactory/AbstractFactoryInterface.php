<?php

namespace Fixin\ResourceManager\AbstractFactory;

use Fixin\ResourceManager\ResourceManagerInterface;

interface AbstractFactoryInterface {

    /**
     * Determins if we can produce a resource by name
     *
     * @param ResourceManagerInterface $manager
     * @param string $name
     * @return bool
     */
    public function canProduce(ResourceManagerInterface $manager, string $name): bool;

    /**
     * Produces resource
     *
     * @param ResourceManagerInterface $manager
     * @param string $name
     */
    public function produce(ResourceManagerInterface $manager, string $name);
}