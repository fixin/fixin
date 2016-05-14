<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\Factory;

use Fixin\Support\ContainerInterface;

interface FactoryInterface {

    /**
     * Produce resource
     *
     * @param ContainerInterface $container
     * @param string $name
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, string $name);
}