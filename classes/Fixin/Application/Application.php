<?php

namespace Fixin\Application;

class Application implements ApplicationInterface {

    const CLASS_KEY = 'class';
    const CONFIG_CLASS_KEY = 'configClass';

    /**
     * @var \Fixin\Support\ContainerInterface
     */
    protected $container;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        // Resource Manager config
        $containerConfig = $config['resourceManager'];
        unset($config['resourceManager']);

        // Classes
        $containerClass = $containerConfig[static::CLASS_KEY] ?? 'Fixin\ResourceManager\ResourceManager';
        unset($containerConfig['class']);

        $configClass = $containerConfig[static::CONFIG_CLASS_KEY] ?? 'Fixin\Base\Config\Config';
        unset($containerConfig['configClass']);

        // Resoure Manager init
        $this->container =
        $rm = new $containerClass($containerConfig);
        $rm->setResource(ApplicationInterface::CONFIG_KEY, new $configClass($config));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run() {
    }
}