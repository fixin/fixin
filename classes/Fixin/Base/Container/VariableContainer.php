<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

use Fixin\Resource\Prototype;

class VariableContainer extends Prototype implements VariableContainerInterface {

    protected $data = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::clear()
     */
    public function clear(): VariableContainerInterface {
        $this->data = [];

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
     * @see \Fixin\Base\Container\VariableContainerInterface::set($name, $value)
     */
    public function set(string $name, $value): VariableContainerInterface {
        $this->data[$name] = $value;

        return $this;
    }
    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Container\VariableContainerInterface::setFrom($values)
     */
    public function setFrom(array $values): VariableContainerInterface {
        $this->data = $values + $this->data;

        return $this;
    }
}