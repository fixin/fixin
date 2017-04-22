<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected $values = [];

    public function __debugInfo()
    {
        return $this->values;
    }

    public function get(string $name, $default = null)
    {
        return $this->values[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($this->values[$name]);
    }

    public function serialize(): string
    {
        return serialize($this->values);
    }

    public function unserialize($serialized): void
    {
        $this->values = unserialize($serialized);
    }

    /**
     * @return static
     */
    public function withValues(array $values): ContainerInterface
    {
        $clone = clone $this;
        $clone->values = $values;

        return $clone;
    }
}
