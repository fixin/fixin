<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\ResourceManagerInterface;

class Application implements ApplicationInterface {

    const CONFIG_APPLICATION = 'application';
    const CONFIG_RESOURCE_MANAGER = 'resourceManager';

    const DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\Resource\ResourceManager';

    const INTERNAL_SERVER_ERROR_HEADER = 'HTTP/1.1 500 Internal Server Error';
    const INTERNAL_SERVER_ERROR_HTML = '<h1>500 Internal server error</h1>';

    const OPTION_CARGO = 'cargo';
    const OPTION_CLASS = 'class';
    const OPTION_ERROR_ROUTE = 'errorRoute';
    const OPTION_ROUTE = 'route';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        // Config
        $this->config = $config[static::CONFIG_APPLICATION] ?? [];

        // Resource Manager config
        $containerConfig = $config[static::CONFIG_RESOURCE_MANAGER];

        // Class
        $containerClass = $containerConfig[static::OPTION_CLASS] ?? static::DEFAULT_RESOURCE_MANAGER_CLASS;
        unset($containerConfig[static::OPTION_CLASS]);

        // Resoure Manager init
        $this->container = new $containerClass($containerConfig);
    }

    /**
     * Error route
     *
     * @param CargoInterface $cargo
     */
    protected function errorRoute(CargoInterface $cargo) {
        try {
            $cargo = $this->container->get($this->config[static::OPTION_ERROR_ROUTE])->handle($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            // Double error
            $this->internalServerError($t->getMessage());
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

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run(): ApplicationInterface {
        $container = $this->container;

        try {
            $cargo = $container->clonePrototype($this->config[static::OPTION_CARGO]);
            $cargo = $container->get($this->config[static::OPTION_ROUTE])->handle($cargo);
            $cargo->unpack();
        }
        catch (\Throwable $t) {
            $this->errorRoute(($cargo ?? $container->clonePrototype('Delivery\Cargo\Cargo'))->setContent($t));
        }

        return $this;
    }
}