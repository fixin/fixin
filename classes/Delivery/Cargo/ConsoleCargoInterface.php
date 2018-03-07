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

    /**
     * Get arguments
     *
     * @return VariableContainerInterface
     */
    public function getArguments(): VariableContainerInterface;

    /**
     * Get command
     *
     * @return null|string
     */
    public function getCommand(): ?string;

    /**
     * Get environment
     *
     * @return ContainerInterface
     */
    public function getEnvironment(): ContainerInterface;

    /**
     * Get options
     *
     * @return VariableContainerInterface
     */
    public function getOptions(): VariableContainerInterface;

    /**
     * Get script name
     *
     * @return string
     */
    public function getScriptName(): string;

    /**
     * Get server
     *
     * @return ContainerInterface
     */
    public function getServer(): ContainerInterface;
}
