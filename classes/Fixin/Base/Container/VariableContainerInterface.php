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

        public function set(string $name, $value): self;

    /**
     * Set values from array
     */
    public function setFromArray(array $values): self;

    public function setModified(bool $modified): self;
    public function unset(string $name): self;
}
