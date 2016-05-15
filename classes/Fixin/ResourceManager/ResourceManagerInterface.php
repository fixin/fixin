<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Fixin\ResourceManager;

use Fixin\Support\ContainerInterface;

interface ResourceManagerInterface extends ContainerInterface {

    /**
     * Clone the registered prototype
     *
     * @param string $name
     * @return PrototypeInterface
     */
    public function clonePrototype(string $name);
}