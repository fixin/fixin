<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

class VariableContainer implements VariableContainerInterface
{
    use ContainerTrait;

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @return $this
     */
    public function clear(): VariableContainerInterface
    {
        $this->values = [];
        $this->modified = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function delete(string $name): VariableContainerInterface
    {
        unset($this->values[$name]);
        $this->modified = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $keys): VariableContainerInterface
    {
        $this->values = array_diff_key($this->values, array_flip($keys));
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    public function serialize(): string
    {
        return serialize($this->values);
    }

    /**
     * @return $this
     */
    public function set(string $name, $value): VariableContainerInterface
    {
        $this->values[$name] = $value;
        $this->modified = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function setModified(bool $modified): VariableContainerInterface
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @return $this
     */
    public function setMultiple(array $values): VariableContainerInterface
    {
        $this->values = $values + $this->values;
        $this->modified = true;

        return $this;
    }

    public function unserialize($serialized): void
    {
        $this->values = unserialize($serialized);
    }
}
