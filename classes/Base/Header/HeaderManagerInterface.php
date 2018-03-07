<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Header;

use Fixin\Resource\PrototypeInterface;

interface HeaderManagerInterface extends PrototypeInterface
{
    public const
        VALUES = 'values';

    /**
     * Add header
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function add(string $name, string $value): HeaderManagerInterface;

    /**
     * Clear headers
     *
     * @return $this
     */
    public function clear(): HeaderManagerInterface;

    /**
     * Get value list
     *
     * @param string $name
     * @param array $default
     * @return array
     */
    public function get(string $name, array $default = []): array;

    /**
     * Determine if has given header
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Send headers
     *
     * @return $this
     */
    public function send(): HeaderManagerInterface;

    /**
     * Set header value list
     *
     * @param string $name
     * @param array $values
     * @return $this
     */
    public function set(string $name, array $values): HeaderManagerInterface;
}
