<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends ContainerInterface {

    /**
     * Clear all values
     *
     * @return VariableContainerInterface
     */
    public function clear(): VariableContainerInterface;

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
}