<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

class Application implements ApplicationInterface {

    const DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\Resource\ResourceManager';
    const DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS = 'Fixin\Base\Config\Config';

    const INTERNAL_SERVER_ERROR_HEADER = 'HTTP/1.1 500 Internal Server Error';
    const INTERNAL_SERVER_ERROR_HTML = '<h1>500 Internal server error</h1>';

    const KEY_APPLICATION = 'application';
    const KEY_CARGO = 'cargo';
    const KEY_CLASS = 'class';
    const KEY_CONFIG = 'config';
    const KEY_CONFIG_CLASS = 'configClass';
    const KEY_ERROR_ROUTE = 'errorRoute';
    const KEY_RESOURCE_MANAGER = 'resourceManager';
    const KEY_ROUTE = 'route';

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
        $this->config = $config[static::KEY_APPLICATION] ?? [];
        unset($config[static::KEY_APPLICATION]);

        // Resource Manager config
        $containerConfig = $config[static::KEY_RESOURCE_MANAGER];
        unset($config[static::KEY_RESOURCE_MANAGER]);

        // Classes
        $containerClass = $containerConfig[static::KEY_CLASS] ?? static::DEFAULT_RESOURCE_MANAGER_CLASS;
        unset($containerConfig[static::KEY_CLASS]);

        $configClass = $containerConfig[static::KEY_CONFIG_CLASS] ?? static::DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS;
        unset($containerConfig[static::KEY_CONFIG_CLASS]);

        // Config
        $containerConfig['resources'][static::KEY_CONFIG] = new $configClass($config);

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
            $cargo = $container->get($this->config[static::KEY_ERROR_ROUTE])->dispatch($cargo);
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

        $sessionManager = $container->get('Base\Session\SessionManager');
        $sessionManager->getSession('abcde');

        try {
            // Normal dispatch
            $cargo = $container->clonePrototype($this->config[static::KEY_CARGO]);
            $cargo = $container->get($this->config[static::KEY_ROUTE])->dispatch($cargo);
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