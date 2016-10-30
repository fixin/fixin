<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends ContainerInterface, \Serializable {

    /**
     * Clear all values
     *
     * @return VariableContainerInterface
     */
    public function clear(): VariableContainerInterface;

    /**
     * Determine if content modified
     *
     * @return bool
     */
    public function isModified(): bool;

    /**
     * Set value for key
     *
     * @param string $name
     * @param mixed $value
     * @return VariableContainerInterface
     */
    public function set(string $name, $value): VariableContainerInterface;

    /**
     * Set values from array
     *
     * @param array $values
     * @return VariableContainerInterface
     */
    public function setFromArray(array $values): VariableContainerInterface;

    /**
     * Set modified state
     *
     * @param bool $modified
     * @return VariableContainerInterface
     */
    public function setModified(bool $modified): VariableContainerInterface;
}