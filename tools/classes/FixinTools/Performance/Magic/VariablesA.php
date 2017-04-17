<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\Performance\Magic;

/**
 * @property callable summary
 * @property callable summary1
 * @property callable summary2
 * @property callable summary3
 */
class VariablesA implements \ArrayAccess
{
    protected $test = 'test';

    /**
     * @var array
     */
    protected $variables = [
        'summary' => 'This is a test summary.',
        'x' => ['a' => 0]
    ];

    public function &__get(string $name)
    {
        return $this->variables[$name];
    }

    public function __isset(string $name): bool
    {
        return isset($this->variables[$name]);
    }

    public function __set(string $name, $value): void
    {
        $this->variables[$name] = $value;
    }

    /**
     * Simple getter
     */
    public function getTest(): string
    {
        return $this->test;
    }

    public function getVariable(string $name)
    {
        return $this->variables[$name] ?? null;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function hasVariable(string $name): bool
    {
        return isset($this->variables[$name]);
    }

    public function offsetExists($offset)
    {
        return isset($this->variables[$offset]);
    }

    public function &offsetGet($offset)
    {
        return $this->variables[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->variables[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->variables[$offset]);
    }

    public function setVariable(string $key, $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables + $this->variables;

        return $this;
    }
}
