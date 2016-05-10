<?php

namespace Fixin\ResourceManager\Factory;

use Fixin\ResourceManager\ResourceManagerInterface;

interface FactoryInterface {

    /**
     * Produces resource
     *
     * @param ResourceManagerInterface $manager
     */
    public function produce(ResourceManagerInterface $manager);
}