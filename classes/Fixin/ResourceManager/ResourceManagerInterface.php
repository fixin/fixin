<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Fixin\ResourceManager;

use Fixin\Support\ContainerInterface;
use Fixin\Support\PrototypeInterface;

interface ResourceManagerInterface extends ContainerInterface {

    /**
     * Clone the registered prototype
     *
     * @param string $name
     * @return PrototypeInterface
     */
    public function clonePrototype(string $name);
}