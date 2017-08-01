<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTest;

use Fixin\Resource\ResourceInterface;
use Fixin\Resource\ResourceManagerInterface;

class ResourceManager implements ResourceManagerInterface
{
    protected $resources;

    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    public function clone(string $name, string $class, array $options = [])
    {
        $instance = $this->resources[$name] ?? null;

        if ($instance instanceof ResourceInterface) {
            throw new \Exception("'$name' is a resource");
        }

        if ($instance) {
            return clone $this->resources[$name];
        }

        throw new \Exception("Prototype not exists '$name'");
    }

    public function get(string $name, string $class)
    {
        $instance = $this->resources[$name] ?? null;

        if (!$instance instanceof ResourceInterface) {
            throw new \Exception("'$name' is not a resource");
        }

        if ($instance) {
            return $instance;
        }

        throw new \Exception("Resource not exists '$name'");
    }

    public function has(string $name): bool
    {
        return isset($this->resources[$name]);
    }
}
