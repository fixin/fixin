<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\AbstractFactory;

use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\ContainerInterface;

interface AbstractFactoryInterface extends FactoryInterface {

    /**
     * Determine if we can produce a resource by name
     *
     * @param ContainerInterface $container
     * @param string $name
     * @return bool
     */
    public function canProduce(ContainerInterface $container, string $name): bool;
}