<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

use Fixin\Resource\ResourceInterface;

interface ContainerInterface extends ResourceInterface
{
    public const
        VALUES = 'values';

    /**
     * Get value for key or return default value for non-set key
     */
    public function get(string $name, $default = null);

    /**
     * Determine the key has value
     */
    public function has(string $name): bool;
}
