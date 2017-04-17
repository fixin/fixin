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
    public function clone(string $name, string $class, array $options = []);

    /**
     * Get instance
     *
     * @return ($class)|ResourceInterface|null
     */
    public function get(string $name, string $class): ?ResourceInterface;

    /**
     * Check if the name has been accessible
     */
    public function has(string $name): bool;
}