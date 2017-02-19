<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

class VariableContainer implements VariableContainerInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @return static
     */
    public function clear(): VariableContainerInterface
    {
        $this->data = [];
        $this->modified = true;

        return $this;
    }

    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    public function serialize(): string
    {
        return serialize($this->data);
    }

    /**
     * @return static
     */
    public function set(string $name, $value): VariableContainerInterface
    {
        $this->data[$name] = $value;
        $this->modified = true;

        return $this;
    }

    /**
     * @return static
     */
    public function setFromArray(array $values): VariableContainerInterface
    {
        $this->data = $values + $this->data;
        $this->modified = true;

        return $this;
    }

    /**
     * @return static
     */
    public function setModified(bool $modified): VariableContainerInterface
    {
        $this->modified = $modified;

        return $this;
    }

    public function unserialize($serialized): void
    {
        $this->data = unserialize($serialized);
    }

    /**
     * @return static
     */
    public function unset(string $name): VariableContainerInterface
    {
        unset($this->data[$name]);
        $this->modified = true;

        return $this;
    }
}
