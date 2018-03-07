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
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Determine the key has value
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;
}