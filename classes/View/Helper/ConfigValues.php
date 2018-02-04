<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Resource\ResourceManagerInterface;

class ConfigValues extends AbstractHelper
{
    /**
     * @var ContainerInterface
     */
    protected $__config;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->__config = $resourceManager->get('config', ContainerInterface::class);
    }

    public function __get(string $name)
    {
        return $this->$name = $this->__config->get($name);
    }

    public function __invoke($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        return $this->__config->get($name);
    }
}
