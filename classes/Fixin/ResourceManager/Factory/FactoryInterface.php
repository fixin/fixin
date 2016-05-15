<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\Factory;

use Fixin\ResourceManager\ResourceManagerInterface;

interface FactoryInterface {

    /**
     * Produce resource
     *
     * @param ResourceManagerInterface $container
     * @param string $name
     * @return mixed
     */
    public function __invoke(ResourceManagerInterface $container, string $name = null);
}