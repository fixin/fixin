<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

interface ContainerInterface extends PrototypeInterface {

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