<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Delivery\Cargo\ConsoleCargoInterface;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;

class ConsoleCargoFactory implements FactoryInterface
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null, string $name = null): ConsoleCargoInterface
    {
        $arguments = $_SERVER['argv'];
        $scriptName = array_shift($arguments);
        $options = $this->processArguments($resourceManager, $arguments)
            + [
                ConsoleCargoInterface::ENVIRONMENT => $resourceManager->get('*\Support\Factory\EnvironmentInfoFactory', ContainerInterface::class),
                ConsoleCargoInterface::SCRIPT_NAME => $scriptName,
                ConsoleCargoInterface::SERVER => $resourceManager->get('*\Support\Factory\ServerInfoFactory', ContainerInterface::class)
            ];

        return $resourceManager->clone('*\Delivery\Cargo\ConsoleCargo', ConsoleCargoInterface::class, $options);
    }

    protected function processArguments(ResourceManagerInterface $resourceManager, array $items): array
    {
        $command = null;
        $arguments = [];
        $options = [];

        foreach ($items as $item) {
            if (isset($item[2]) && $item[0] === '-' && $item[1] === '-') {
                $tags = explode('=', substr($item, 2), 2);

                $options[$tags[0]] = $tags[1] ?? true;

                continue;
            }

            $arguments[] = $item;
        }

        if ($arguments) {
            $command = array_shift($arguments);
        }

        return [
            ConsoleCargoInterface::ARGUMENTS => $resourceManager->clone('*\Base\Container\VariableContainer', VariableContainerInterface::class)->replace($arguments),
            ConsoleCargoInterface::COMMAND => $command,
            ConsoleCargoInterface::OPTIONS => $resourceManager->clone('*\Base\Container\VariableContainer', VariableContainerInterface::class)->replace($options)
        ];
    }
}
