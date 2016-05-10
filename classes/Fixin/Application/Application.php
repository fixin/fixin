<?php

namespace Fixin\Application;

use Fixin\Config\Config;

class Application implements ApplicationInterface {

    /**
     * @var \Fixin\Support\ContainerInterface
     */
    protected $container;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        // Resource Manager config
        $rmConfig = $config['resourceManager'];
        unset($config['resourceManager']);

        $rmClass = $rmConfig['class'] ?? '\Fixin\ResourceManager\ResourceManager';
        unset($rmConfig['class']);

        // Resoure Manager init
        $this->resourceManager =
        $rm = new $rmClass($rmConfig);
        $rm->setResource(ApplicationInterface::CONFIG_KEY, new Config($config));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run() {
    }
}