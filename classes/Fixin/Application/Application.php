<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Cargo\Factory\HttpCargoFactory;

class Application implements ApplicationInterface {

    const CLASS_KEY = 'class';
    const CONFIG_CLASS_KEY = 'configClass';
    const CONFIG_KEY = 'config';
    const RESOURCE_MANAGER_KEY = 'resourceManager';

    /**
     * @var \Fixin\Support\ContainerInterface
     */
    protected $container;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        // Resource Manager config
        $containerConfig = $config[static::RESOURCE_MANAGER_KEY];
        unset($config[static::RESOURCE_MANAGER_KEY]);

        // Classes
        $containerClass = $containerConfig[static::CLASS_KEY] ?? 'Fixin\ResourceManager\ResourceManager';
        unset($containerConfig[static::CLASS_KEY]);

        $configClass = $containerConfig[static::CONFIG_CLASS_KEY] ?? 'Fixin\Base\Config\Config';
        unset($containerConfig[static::CONFIG_CLASS_KEY]);

        // Resoure Manager init
        $this->container =
        $rm = new $containerClass($containerConfig);
        $rm->setResource(static::CONFIG_KEY, new $configClass($config));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run() {
        $cargo = (new HttpCargoFactory())($this->container);

        echo $cargo;

//         echo $cargo->getRequestUri();
    }
}