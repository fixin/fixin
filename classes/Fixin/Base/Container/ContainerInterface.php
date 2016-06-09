<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

interface ContainerInterface {

    /**
     * Get value for key or return default value for not-set key
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * Determine the key has value
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}