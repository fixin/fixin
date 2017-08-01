<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Container\VariableContainerInterface;

interface ConsoleCargoInterface extends CargoInterface
{
    public const
        ARGUMENTS = 'arguments',
        COMMAND = 'command',
        ENVIRONMENT = 'environment',
        OPTIONS = 'options',
        SCRIPT_NAME = 'scriptName',
        SERVER = 'server';

    public function getArguments(): VariableContainerInterface;
    public function getCommand(): ?string;
    public function getEnvironment(): ContainerInterface;
    public function getOptions(): VariableContainerInterface;
    public function getScriptName(): string;
    public function getServer(): ContainerInterface;
}
