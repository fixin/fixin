<?php

namespace Fixin\ResourceManager\Factory;

use Fixin\ResourceManager\ResourceManagerInterface;

interface FactoryInterface {

    /**
     * Produces resource
     *
     * @param ResourceManagerInterface $manager
     * @param string $name
     */
    public function produce(ResourceManagerInterface $manager, string $name);
}