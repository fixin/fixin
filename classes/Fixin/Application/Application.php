<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Application;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Http;
use Throwable;

class Application implements ApplicationInterface
{
    protected const
        DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\Resource\ResourceManager',
        INTERNAL_SERVER_ERROR_CODE = 500,
        INTERNAL_SERVER_ERROR_HEADER = 'HTTP/1.1 500 Internal Server Error',
        INTERNAL_SERVER_ERROR_HTML = '<h1>500 Internal server error</h1>';

    public const
        CONFIG_APPLICATION = 'application',
        CONFIG_RESOURCE_MANAGER = 'resourceManager',
        OPTION_CARGO = 'cargo',
        OPTION_CLASS = 'class',
        OPTION_ERROR_ROUTE = 'errorRoute',
        OPTION_ROUTE = 'route';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    public function __construct(array $config)
    {
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

    protected function errorRoute(CargoInterface $cargo): void
    {
        // HTTP cargo
        if ($cargo instanceof HttpCargoInterface) {
            $cargo->setStatusCode(Http::STATUS_INTERNAL_SERVER_ERROR_500);
        }

        try {
            $this->container->get($this->config[static::OPTION_ERROR_ROUTE])
                ->handle($cargo)
                ->unpack();
        }
        catch (Throwable $t) {
            // Double error
            $this->internalServerError($t->getMessage());
        }
    }

    protected function internalServerError(string $text): void
    {
        header(static::INTERNAL_SERVER_ERROR_HEADER, true, static::INTERNAL_SERVER_ERROR_CODE);
        echo static::INTERNAL_SERVER_ERROR_HTML;

        echo $text;
        exit;
    }

    /**
     * @return static
     */
    public function run(): ApplicationInterface
    {
        $container = $this->container;

        // TODO lock

        try {
            /** @var CargoInterface $cargo */
            $cargo = $container->clone($this->config[static::OPTION_CARGO]);
            $container->get($this->config[static::OPTION_ROUTE])
                ->handle($cargo)
                ->unpack();
        }
        catch (Throwable $t) {
            $this->errorRoute(($cargo ?? $container->clone('Delivery\Cargo\Cargo'))->setContent($t));
        }

        return $this;
    }
}
