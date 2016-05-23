<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\Cargo;
use Fixin\View\View;

class Application implements ApplicationInterface {

    const DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\ResourceManager\ResourceManager';
    const DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS = 'Fixin\Base\Config\Config';

    const INTERNAL_SERVER_ERROR_HEADER = 'HTTP/1.1 500 Internal Server Error';
    const INTERNAL_SERVER_ERROR_HTML = '<h1>500 Internal server error</h1>';

    const KEY_APPLICATION_DISPATCHER = 'applicationDispatcher';
    const KEY_CARGO = 'cargo';
    const KEY_CLASS = 'class';
    const KEY_CONFIG = 'config';
    const KEY_CONFIG_CLASS = 'configClass';
    const KEY_ERROR_DISPATCHER = 'errorDispatcher';
    const KEY_RESOURCE_MANAGER = 'resourceManager';

    /**
     * @var \Fixin\ResourceManager\ResourceManagerInterface
     */
    protected $container;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
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
        try {
            if ($cargo instanceof \Throwable) {
                $cargo = (new Cargo())->setContent($cargo);
            }

            // Error dispatch
            $cargo = $this->container->clonePrototype(static::KEY_ERROR_DISPATCHER)->dispatch($cargo);
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
            $cargo = $container->clonePrototype(static::KEY_CARGO);
            $cargo = $container->clonePrototype(static::KEY_APPLICATION_DISPATCHER)->dispatch($cargo);
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