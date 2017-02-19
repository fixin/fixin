<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends ContainerInterface, \Serializable
{
    public function clear(): VariableContainerInterface;

    /**
     * Determine if content modified
     */
    public function isModified(): bool;

    public function set(string $name, $value): VariableContainerInterface;

    /**
     * Set values from array
     */
    public function setFromArray(array $values): VariableContainerInterface;

    public function setModified(bool $modified): VariableContainerInterface;
    public function unset(string $name): VariableContainerInterface;
}
