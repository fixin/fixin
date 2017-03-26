<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Headers;

use Fixin\Resource\PrototypeInterface;

interface HeadersInterface extends PrototypeInterface
{
    public const
        VALUES = 'values';

    /**
     * @return $this
     */
    public function add(string $name, string $value): HeadersInterface;

    /**
     * @return $this
     */
    public function clear(): HeadersInterface;

    public function get(string $name, array $default = []): array;
    public function has(string $name): bool;

    /**
     * @return $this
     */
    public function send(): HeadersInterface;

    /**
     * @return $this
     */
    public function set(string $name, array $values): HeadersInterface;
}
