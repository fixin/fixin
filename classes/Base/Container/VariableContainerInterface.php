<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

use Fixin\Resource\PrototypeInterface;

interface VariableContainerInterface extends ContainerInterface, PrototypeInterface, \Serializable
{
    /**
     * Clear all values
     *
     * @return $this
     */
    public function clear(): VariableContainerInterface;

    /**
     * Delete value of key
     *
     * @param string $key
     * @return $this
     */
    public function delete(string $key): VariableContainerInterface;

    /**
     * Delete multiple items
     *
     * @param array $keys
     * @return $this
     */
    public function deleteMultiple(array $keys): VariableContainerInterface;

    /**
     * Determine if is modified
     *
     * @return bool
     */
    public function isModified(): bool;

    /**
     * Set value
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value): VariableContainerInterface;

    /**
     * Set modified
     *
     * @param bool $modified
     * @return $this
     */
    public function setModified(bool $modified): VariableContainerInterface;

    /**
     * Set multiple values
     *
     * @param array $values
     * @return $this
     */
    public function setMultiple(array $values): VariableContainerInterface;
}