<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

class VariableContainer extends Container implements VariableContainerInterface
{
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

    public function isModified(): bool
    {
        return $this->modified;
    }

    /**
     * @return $this
     */
    public function replace(array $values): VariableContainerInterface
    {
        $this->values = $values + $this->values;
        $this->modified = true;

        return $this;
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
    public function unset(string $name): VariableContainerInterface
    {
        unset($this->values[$name]);
        $this->modified = true;

        return $this;
    }
}
