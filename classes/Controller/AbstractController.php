<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Controller;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Resource\Resource;
use Fixin\View\ViewInterface;

abstract class AbstractController extends Resource implements ControllerInterface
{
    private $config;

    protected function createView(?string $template, array $variables = []): ViewInterface
    {
        return $this->resourceManager->clone('*\View\View', ViewInterface::class, [
            ViewInterface::TEMPLATE => $template,
            ViewInterface::VARIABLES => $variables
        ]);
    }

    protected function getConfigValue(string $name)
    {
        if (!$this->config) {
            $this->config = $this->resourceManager->get('config', ContainerInterface::class);
        }

        return $this->config->get($name);
    }
}
