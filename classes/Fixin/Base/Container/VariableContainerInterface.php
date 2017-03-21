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
    public function clear(): VariableContainerInterface;
    public function isModified(): bool;
    public function replace(array $values): VariableContainerInterface;
    public function set(string $name, $value): VariableContainerInterface;
    public function setModified(bool $modified): VariableContainerInterface;
    public function unset(string $name): VariableContainerInterface;
}
