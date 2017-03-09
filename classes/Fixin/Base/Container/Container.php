<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

use Fixin\Resource\Prototype;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected $values = [];

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

    protected function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function unserialize($serialized): void
    {
        $this->values = unserialize($serialized);
    }
}
