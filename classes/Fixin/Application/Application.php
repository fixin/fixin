<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Base\Storage\Directory\Index;

class Application implements ApplicationInterface {

    const CONFIG_APPLICATION = 'application';
    const CONFIG_RESOURCE_MANAGER = 'resourceManager';

    const DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\Resource\ResourceManager';
    const DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS = 'Fixin\Base\Config\Config';

    const INTERNAL_SERVER_ERROR_HEADER = 'HTTP/1.1 500 Internal Server Error';
    const INTERNAL_SERVER_ERROR_HTML = '<h1>500 Internal server error</h1>';

    const OPTION_CARGO = 'cargo';
    const OPTION_CLASS = 'class';
    const OPTION_CONFIG_CLASS = 'configClass';
    const OPTION_ERROR_ROUTE = 'errorRoute';
    const OPTION_ROUTE = 'route';

    const RESOURCE_CONFIG = 'config';

    protected $config;

    /**
     * @var \Fixin\Resource\ResourceManagerInterface
     */
    protected $container;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        // Config
        $this->config = $config[static::CONFIG_APPLICATION] ?? [];
        unset($config[static::CONFIG_APPLICATION]);

        // Resource Manager config
        $containerConfig = $config[static::CONFIG_RESOURCE_MANAGER];
        unset($config[static::CONFIG_RESOURCE_MANAGER]);

        // Classes
        $containerClass = $containerConfig[static::OPTION_CLASS] ?? static::DEFAULT_RESOURCE_MANAGER_CLASS;
        unset($containerConfig[static::OPTION_CLASS]);

        $configClass = $containerConfig[static::OPTION_CONFIG_CLASS] ?? static::DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS;
        unset($containerConfig[static::OPTION_CONFIG_CLASS]);

        // Config
        $containerConfig['resources'][static::RESOURCE_CONFIG] = new $configClass($config);

        // Resoure Manager init
        $this->container = new $containerClass($containerConfig);
    }

    /**
     * Error route
     *
     * @param \Throwable|CargoInterface $cargo
     * @throws \Throwable
     */
    protected function errorRoute($cargo) {
        $container = $this->container;

        try {
            if ($cargo instanceof \Throwable) {
                $cargo = $container->clonePrototype('Delivery\Cargo\Cargo')->setContent($cargo);
            }

            // Error dispatch
            $cargo = $container->get($this->config[static::OPTION_ERROR_ROUTE])->dispatch($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            // Double error
            $this->internalServerError($t->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run() {
        $container = $this->container;

        try {
            // Normal dispatch
            $cargo = $container->clonePrototype($this->config[static::OPTION_CARGO]);
            $cargo = $container->get($this->config[static::OPTION_ROUTE])->dispatch($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            $this->errorRoute(isset($cargo) ? $cargo->setContent($t) : $t);
        }
    }

    /**
     * Internal Server Error
     *
     * @param string $text
     */
    protected function internalServerError(string $text) {
        header(static::INTERNAL_SERVER_ERROR_HEADER, true, 500);
        echo static::INTERNAL_SERVER_ERROR_HTML;

        echo $text;
        exit;
    }
}