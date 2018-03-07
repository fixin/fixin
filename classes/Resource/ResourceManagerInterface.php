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
     * @param string $name
     * @param string $expectedClass
     * @param array $options
     * @return $expectedClass
     */
    public function clone(string $name, string $expectedClass, array $options = []): object;

    /**
     * Get resource
     *
     * @param string $name
     * @param string $expectedClass
     * @return $expectedClass
     */
    public function get(string $name, string $expectedClass): ResourceInterface;

    /**
     * Check if the name has been accessible
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}
