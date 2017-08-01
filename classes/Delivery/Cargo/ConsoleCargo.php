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
use Fixin\Support\DebugDescriptionTrait;
use Fixin\Support\Types;

class ConsoleCargo extends Cargo implements ConsoleCargoInterface
{
    use DebugDescriptionTrait;

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::ARGUMENTS => VariableContainerInterface::class,
            self::COMMAND => [Types::STRING, Types::NULL],
            self::ENVIRONMENT => ContainerInterface::class,
            self::OPTIONS => VariableContainerInterface::class,
            self::SCRIPT_NAME => [Types::STRING],
            self::SERVER => ContainerInterface::class
        ];

    /**
     * @var VariableContainerInterface
     */
    protected $arguments;

    /**
     * @var string|null
     */
    protected $command;

    /**
     * @var ContainerInterface
     */
    protected $environment;

    /**
     * @var VariableContainerInterface
     */
    protected $options;

    /**
     * @var string
     */
    protected $scriptName;

    /**
     * @var ContainerInterface
     */
    protected $server;

    public function getArguments(): VariableContainerInterface
    {
        return $this->arguments;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getEnvironment(): ContainerInterface
    {
        return $this->environment;
    }

    public function getOptions(): VariableContainerInterface
    {
        return $this->options;
    }

    public function getScriptName(): string
    {
        return $this->scriptName;
    }

    public function getServer(): ContainerInterface
    {
        return $this->server;
    }
}
