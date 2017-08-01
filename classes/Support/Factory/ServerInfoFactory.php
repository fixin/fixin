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
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;

class ServerInfoFactory implements FactoryInterface
{
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        return new Container($resourceManager, [Container::VALUES => $_SERVER], $name);
    }
}
