<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

class VariableContainer implements VariableContainerInterface {

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::clear()
     */
    public function clear(): VariableContainerInterface {
        $this->data = [];
        $this->modified = true;

        return $this;
    }
    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\ContainerInterface::get($name)
     */
    public function get(string $name, $default = null) {
        return $this->data[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\ContainerInterface::has($name)
     */
    public function has(string $name): bool {
        return isset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::isModified()
     */
    public function isModified(): bool {
        return $this->modified;
    }

    /**
     * {@inheritDoc}
     * @see Serializable::serialize()
     */
    public function serialize() {
        return serialize($this->data);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::set($name, $value)
     */
    public function set(string $name, $value): VariableContainerInterface {
        $this->data[$name] = $value;
        $this->modified = true;

        return $this;
    }
    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::setFromArray($values)
     */
    public function setFromArray(array $values): VariableContainerInterface {
        $this->data = $values + $this->data;
        $this->modified = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::setModified()
     */
    public function setModified(bool $modified): VariableContainerInterface {
        $this->modified = $modified;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized) {
        return $this->data = unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::unset()
     */
    public function unset(string $name): VariableContainerInterface {
        unset($this->data[$name]);
        $this->modified = true;

        return $this;
    }
}