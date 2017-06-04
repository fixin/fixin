<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Application;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Delivery\Route\RouteInterface;
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
        APPLICATION_ROOT = 'application',
        CARGO = 'cargo',
        ERROR_ROUTE = 'errorRoute',
        RESOURCE_MANAGER_CLASS = 'resourceManagerClass',
        RESOURCE_MANAGER_ROOT = 'resourceManager',
        ROUTE = 'route';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ResourceManagerInterface
     */
    protected $resourceManager;

    public function __construct(array $config)
    {
        // Config
        $this->config = $config[static::APPLICATION_ROOT] ?? [];

        // Resource Manager config
        $resourceManagerConfig = $config[static::RESOURCE_MANAGER_ROOT];

        // Class
        $resourceManagerClass = $resourceManagerConfig[static::RESOURCE_MANAGER_CLASS] ?? static::DEFAULT_RESOURCE_MANAGER_CLASS;
        unset($resourceManagerConfig[static::RESOURCE_MANAGER_CLASS]);

        // Resource Manager init
        $this->resourceManager = new $resourceManagerClass($resourceManagerConfig);
    }

    protected function errorRoute(CargoInterface $cargo): void
    {
        // HTTP cargo
        if ($cargo instanceof HttpCargoInterface) {
            $cargo->setStatusCode(Http::STATUS_INTERNAL_SERVER_ERROR_500);
        }

        try {
            $this->resourceManager->get($this->config[static::ERROR_ROUTE], RouteInterface::class)
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

        // TODO production mode?
        echo $text;
        exit;
    }

    /**
     * @return $this
     */
    public function run(): ApplicationInterface
    {
        $resourceManager = $this->resourceManager;

        try {
            /** @var CargoInterface $cargo */
            $cargo = $resourceManager->clone($this->config[static::CARGO], CargoInterface::class);
            $resourceManager->get($this->config[static::ROUTE], RouteInterface::class)
                ->handle($cargo)
                ->unpack();
        }
        catch (Throwable $t) {
            try {
                $this->errorRoute(($cargo ?? $resourceManager->clone('*\Delivery\Cargo\Cargo', CargoInterface::class))->setContent($t));
            }
            catch (Throwable $t) {
                $this->internalServerError(get_class($t) . ': ' . $t->getMessage());
            }
        }

        return $this;
    }
}
