<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends ContainerInterface
{
    /**
     * @return $this
     */
    public function clear(): VariableContainerInterface;

    public function isModified(): bool;

    /**
     * @return $this
     */
    public function replace(array $values): VariableContainerInterface;

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
    public function unset(string $name): VariableContainerInterface;
}
