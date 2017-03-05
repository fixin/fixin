<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

class VariableContainer extends Container implements VariableContainerInterface
{
    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @return static
     */
    public function clear(): VariableContainerInterface
    {
        $this->values = [];
        $this->modified = true;

        return $this;
    }

    /**
     * @return static
     */
    public function clearModified(): VariableContainerInterface
    {
        $this->modified = false;

        return $this;
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    /**
     * @return static
     */
    public function set(string $name, $value): VariableContainerInterface
    {
        $this->values[$name] = $value;
        $this->modified = true;

        return $this;
    }

    /**
     * @return static
     */
    public function unset(string $name): VariableContainerInterface
    {
        unset($this->values[$name]);
        $this->modified = true;

        return $this;
    }
}
