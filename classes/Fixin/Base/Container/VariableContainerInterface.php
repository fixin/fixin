<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends ContainerInterface, \Serializable
{
    public function clear(): self;

    /**
     * Determine if content modified
     */
    public function isModified(): bool;

    /**
     * Set value for key
     */
    public function set(string $name, $value): VariableContainerInterface;

    /**
     * Set values from array
     */
    public function setFromArray(array $values): VariableContainerInterface;

    /**
     * Set modified state
     */
    public function setModified(bool $modified): VariableContainerInterface;

    /**
     * Unset value for key
     */
    public function unset(string $name): VariableContainerInterface;
}
