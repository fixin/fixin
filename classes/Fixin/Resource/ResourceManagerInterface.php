<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface ResourceManagerInterface
{
    /**
     * Clone prototype
     *
     * @return ($class)|PrototypeInterface|object|null
     */
    public function clone(string $name, string $expectedClass, array $options = []);

    /**
     * Get instance
     *
     * @return ($class)|ResourceInterface|null
     */
    public function get(string $name, string $expectedClass);

    /**
     * Check if the name has been accessible
     */
    public function has(string $name): bool;
}
