<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support\Factory;

use Fixin\Base\Container\Container;
use Fixin\Base\Container\ContainerInterface;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;

class ServerInfoFactory implements FactoryInterface
{
    /**
     * Produce server info container
     *
     * @param ResourceManagerInterface $resourceManager
     * @param array|null $options
     * @return ContainerInterface
     */
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null): ContainerInterface
    {
        return new Container($resourceManager, [Container::VALUES => $_SERVER]);
    }
}
