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
        CONSOLE_FATAL_ERROR_TEXT = 'Fatal Error',
        DEFAULT_RESOURCE_MANAGER_CLASS = 'Fixin\Resource\ResourceManager',
        HTTP_FATAL_ERROR_CODE = 500,
        HTTP_FATAL_ERROR_HEADER = 'HTTP/1.1 500 Internal Server Error',
        HTTP_FATAL_ERROR_HTML = '<h1>500 Internal server error</h1>';

    public const
        APPLICATION_ROOT = 'application',
        CARGO = 'cargo',
        ERROR_ROUTE = 'errorRoute',
        RESOURCE_MANAGER_CLASS = 'class',
        RESOURCE_MANAGER_ROOT = 'resourceManager',
        ROUTE = 'route',
        SHOW_FATAL_ERROR = 'showFatalError';

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

    public function displayErrorPage(int $statusCode): ApplicationInterface
    {
        try {
            /** @var CargoInterface $cargo */
            $cargo = $this->resourceManager->clone($this->config[static::CARGO], CargoInterface::class);
            $cargo->setContentType('text/html');

            // HTTP cargo
            if ($cargo instanceof HttpCargoInterface) {
                $cargo->setStatusCode($statusCode);
            }

            $this->errorRoute($cargo);
        }
        catch (Throwable $t) {
            $this->fatalError($t);
        }

        return $this;
    }

    protected function errorRoute(CargoInterface $cargo): void
    {
        try {
            $this->resourceManager->get($this->config[static::ERROR_ROUTE], RouteInterface::class)
                ->handle($cargo)
                ->unpack();
        }
        catch (Throwable $t) {
            // Double error
            $this->fatalError($t);
        }
    }

    protected function fatalError(Throwable $throwable): void
    {
        $text = get_class($throwable) . ': ' . $throwable->getMessage();
        $output = ($this->config[static::SHOW_FATAL_ERROR] ?? true) ? $text : '';

        if (PHP_SAPI === 'cli') {
            echo static::CONSOLE_FATAL_ERROR_TEXT . PHP_EOL;
            echo $output . PHP_EOL;

            exit;
        }

        header(static::HTTP_FATAL_ERROR_HEADER, true, static::HTTP_FATAL_ERROR_CODE);
        echo static::HTTP_FATAL_ERROR_HTML;
        echo htmlspecialchars($output);

        error_log($text);

        exit;
    }

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
                // Missing cargo
                if (!isset($cargo)) {
                    $cargo = $resourceManager->clone('*\Delivery\Cargo\Cargo', CargoInterface::class);
                }

                $cargo->setContent($t);

                // HTTP cargo
                if ($cargo instanceof HttpCargoInterface) {
                    $cargo->setStatusCode(Http::STATUS_INTERNAL_SERVER_ERROR_500);
                }

                $this->errorRoute($cargo);
            }
            catch (Throwable $t) {
                $this->fatalError($t);
            }
        }

        return $this;
    }
}
