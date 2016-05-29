<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Fixin\Resource;

use Fixin\Support\ContainerInterface;

interface ResourceManagerInterface extends ContainerInterface {

    /**
     * Clone the registered prototype
     *
     * @param string $name
     * @return object
     */
    public function clonePrototype(string $name);
}