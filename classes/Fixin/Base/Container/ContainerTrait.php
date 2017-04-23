<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

trait ContainerTrait
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
}
