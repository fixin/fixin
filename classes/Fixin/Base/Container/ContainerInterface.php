<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

interface ContainerInterface extends \Serializable
{
    /**
     * Get value for key or return default value for not-set key
     */
    public function get(string $name, $default = null);

    /**
     * Determine the key has value
     */
    public function has(string $name): bool;

    /**
     * Instance with new values
     */
    public function withValues(array $values): ContainerInterface;
}
