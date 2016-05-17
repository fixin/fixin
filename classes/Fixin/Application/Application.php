<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Delivery\Cargo\CargoInterface;

class Application implements ApplicationInterface {

    const CARGO_KEY = 'cargo';
    const CLASS_KEY = 'class';
    const CONFIG_CLASS_KEY = 'configClass';
    const CONFIG_KEY = 'config';
    const DISPATCHER_KEY = 'dispatcher';
    const ERROR_DISPATCHER_KEY = 'errorDispatcher';
    const RESOURCE_MANAGER_KEY = 'resourceManager';

    const DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\ResourceManager\ResourceManager';
    const DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS = 'Fixin\Base\Config\Config';

    /**
     * @var \Fixin\ResourceManager\ResourceManagerInterface
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
        $containerClass = $containerConfig[static::CLASS_KEY] ?? static::DEFAULT_RESOURCE_MANAGER_CLASS;
        unset($containerConfig[static::CLASS_KEY]);

        $configClass = $containerConfig[static::CONFIG_CLASS_KEY] ?? static::DEFAULT_RESOURCE_MANAGER_CONFIG_CLASS;
        unset($containerConfig[static::CONFIG_CLASS_KEY]);

        // Resoure Manager init
        $this->container =
        $rm = new $containerClass($containerConfig);
        $rm->setResource(static::CONFIG_KEY, new $configClass($config));
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
                throw $cargo;
            }

            // Error dispatch
            $cargo = $container->get(static::ERROR_DISPATCHER_KEY)->dispatch($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            // Double error
            $this->internalServerError($protocolVersion, $t->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run() {
        $container = $this->container;
        $protocolVersion = '1.1';

        try {
            // Normal dispatch
            $cargo = $container->clonePrototype(static::CARGO_KEY);
            $protocolVersion = $cargo->getRequestProtocolVersion();
            $cargo = $container->get(static::DISPATCHER_KEY)->dispatch($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            $this->errorRoute(isset($cargo) ? $cargo->setContent($t) : $t);
        }
    }

    /**
     * Internal Server Error
     *
     * @param string $protocolVersion
     */
    protected function internalServerError(string $protocolVersion, string $text) {
        header("HTTP/$protocolVersion 500 Internal Server Error", true, 500);
        echo '<h1>500 Internal server error</h1>';

        echo $text;
        exit;
    }
}