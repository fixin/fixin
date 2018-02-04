<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends \Serializable
{
    /**
     * @return $this
     */
    public function clear(): VariableContainerInterface;

    /**
     * @return $this
     */
    public function delete(string $name): VariableContainerInterface;

    /**
     * Delete multiple items
     *
     * @param array $keys
     * @return $this
     */
    public function deleteMultiple(array $keys): VariableContainerInterface;

    /**
     * Get value for key or return default value for non-set key
     */
    public function get(string $name, $default = null);

    /**
     * Determine the key has value
     */
    public function has(string $name): bool;

    public function isModified(): bool;

    /**
     * @return $this
     */
    public function set(string $name, $value): VariableContainerInterface;

    /**
     * @return $this
     */
    public function setModified(bool $modified): VariableContainerInterface;

    /**
     * @return $this
     */
    public function setMultiple(array $values): VariableContainerInterface;
}
