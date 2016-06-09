<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Fixin\Resource;

interface ResourceManagerInterface {

    /**
     * Clone the registered prototype
     *
     * @param string $name
     * @param array $options
     * @return object
     */
    public function clonePrototype(string $name, array $options = []);

    /**
     * Get the registered instance
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    /**
     * Check if the name has been registered
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}