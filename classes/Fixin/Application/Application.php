<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Delivery\Cargo\Factory\HttpCargoFactory;

class Application implements ApplicationInterface {

    const CARGO_KEY = 'cargo';
    const CLASS_KEY = 'class';
    const CONFIG_CLASS_KEY = 'configClass';
    const CONFIG_KEY = 'config';
    const DISPATCHER_KEY = 'dispatcher';
    const ERROR_DISPATCHER_KEY = 'errorDispatcher';
    const RESOURCE_MANAGER_KEY = 'resourceManager';

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
        $container = $this->container;

        try {
            // Normal dispatch
            $cargo = $container->clonePrototype(static::CARGO_KEY);
            $cargo = $container->get(static::DISPATCHER_KEY)->dispatch($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            try {
                if (!isset($cargo)) {
                    throw $t;
                }

                // Error dispatch
                $cargo->setContent($t);
                $cargo = $container->get(static::ERROR_DISPATCHER_KEY)->dispatch($cargo);
                $cargo->unpack();
            }
            catch (\Throwable $t) {
                // Double error
                $this->internalServerError('HTTP/' . ($cargo->getRequestProtocolVersion()), $t->getMessage());
            }
        }
    }

    /**
     * Internal Server Error
     * @param string $protocolVersion
     */
    protected function internalServerError(string $protocolVersion, string $text) {
        header("HTTP/$protocolVersion 500 Internal Server Error", true, 500);
        echo '<h1>500 Internal server error</h1>';

        echo $text;
        exit;
    }
}