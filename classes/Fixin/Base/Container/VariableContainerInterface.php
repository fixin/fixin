<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Container;

interface VariableContainerInterface extends ContainerInterface
{
    public function clear(): VariableContainerInterface;
    public function clearModified(): VariableContainerInterface;
    public function isModified(): bool;
    public function set(string $name, $value): VariableContainerInterface;
    public function setValues(array $values): VariableContainerInterface;
    public function unset(string $name): VariableContainerInterface;
}
