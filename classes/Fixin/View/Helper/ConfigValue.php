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

class ConfigValue extends AbstractHelper
{
    /**
     * @var ContainerInterface
     */
    protected $config;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->config = $resourceManager->get('config', ContainerInterface::class);
    }

    public function __invoke($value)
    {
        return $this->get($value);
    }

    public function get($value)
    {
        return $this->config->get($value);
    }
}
