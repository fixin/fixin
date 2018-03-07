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

    /**
     * @inheritDoc
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options)
    {
        parent::__construct($resourceManager, $options);

        $this->__config = $resourceManager->get('config', ContainerInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name)
    {
        return $this->$name = $this->__config->get($name);
    }

    /**
     * @inheritDoc
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Get value
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->__config->get($name);
    }
}
