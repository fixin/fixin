<?php

namespace Fixin\ResourceManager;

interface ResourceManagerInterface {

    /**
     * Gets the registered instance
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    /**
     * Checks if the name has been registered
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}