<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Delivery\Cargo\CargoHandlerInterface;

class HttpRouterHub extends HttpHub
{
    protected const
        EXCEPTION_INVALID_HANDLER = "Invalid handler '%s'",
        EXCEPTION_MISSING_ROUTE_PARAMETER = "Missing route parameter '%s'",
        EXCEPTION_UNKNOWN_ROUTE = "Unknown route '%s'",
        THIS_REQUIRES = [
            self::OPTION_ROUTE_TREE => self::TYPE_ARRAY
        ];

    public const
        KEY_ANY_PARAMETER = ':',
        KEY_HANDLER = 'handler',
        KEY_PARAMETERS = 'parameters',
        KEY_PATTERN_PARAMETER = '?',
        KEY_URI = 'uri',
        OPTION_HANDLERS = 'handlers',
        OPTION_ROUTE_TREE = 'routeTree',
        OPTION_ROUTE_URIS = 'routeUris';

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @var CargoHandlerInterface[]
     */
    protected $loadedHandlers = [];

    /**
     * @var array
     */
    protected $routeTree;

    /**
     * @var array
     */
    protected $routeUris;

    /**
     * Find handler for segments
     */
    protected function findHandler(array $segments, array $node, array $parameters): ?array
    {
        // Handler
        if (empty($segments)) {
            return [
                static::KEY_HANDLER => $node[static::KEY_HANDLER],
                static::KEY_PARAMETERS => array_combine($node[static::KEY_PARAMETERS], $parameters)
            ];
        }

        // Next segment
        $segment = array_shift($segments);

        // Normal segment
        if (isset($node[$segment])) {
            return $this->findHandler($segments, $node[$segment], $parameters);
        }

        // Parameter
        return $this->findHandlerParameter($segments, $node, $parameters, $segment);
    }

    /**
     * Find handler - parameter
     */
    protected function findHandlerParameter(array $segments, array $node, array $parameters, string $segment): ?array
    {
        // Parameter
        $segment = rawurldecode($segment);
        $parameters[] = $segment;

        // Pattern
        if (isset($node[static::KEY_PATTERN_PARAMETER])) {
            if ($result = $this->findHandlerPatternParameter($segments, $node, $parameters, $segment)) {
                return $result;
            }
        }

        // Any
        return isset($node[static::KEY_ANY_PARAMETER]) ? $this->findHandler($segments, $node[static::KEY_ANY_PARAMETER], $parameters) : null;
    }

    /**
     * Find handler - pattern parameter test
     */
    protected function findHandlerPatternParameter(array $segments, array $node, array $parameters, string $segment): ?array
    {
        foreach ($node[static::KEY_PATTERN_PARAMETER] as $pattern => $route) {
            if (preg_match("/^$pattern\$/", $segment) && false !== $result = $this->findHandler($segments, $route, $parameters)) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Get handler instance by name
     */
    protected function getHandler(string $name): CargoHandlerInterface
    {
        return $this->loadedHandlers[$name] ?? ($this->loadedHandlers[$name] = $this->produceHandler($name));
    }

    public function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface
    {
        $segments = explode('/', trim($cargo->getRequestUri()->getPath(), '/'));
        $count = count($segments);

        if (isset($this->routeTree[$count]) && false !== $found = $this->findHandler($segments, $this->routeTree[$count], [])) {
            $cargo->getRequestParameters()->setFromArray($found[static::KEY_PARAMETERS]);

            return $this->getHandler($found[static::KEY_HANDLER])->handle($cargo);
        }

        return $cargo;
    }

    /**
     * Produce handler
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function produceHandler(string $name): CargoHandlerInterface
    {
        $handler = $this->handlers[$name];

        if (is_string($handler)) {
            $handler = $this->container->get($handler);
        }

        if ($handler instanceof CargoHandlerInterface) {
            return $handler;
        }

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_HANDLER, $name));
    }

    /**
     * Build route URI
     */
    public function route(string $name, array $parameters): string
    {
        if (isset($this->routeUris[$name])) {
            return vsprintf($this->routeUris[$name][static::KEY_URI], $this->routeParameters($name, $parameters));
        }

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_UNKNOWN_ROUTE, $name));
    }

    /**
     * Build route parameter array
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function routeParameters(string $name, array $parameters): array
    {
        $replaces = [];

        foreach ($this->routeUris[$name][static::KEY_PARAMETERS] as $key => $required) {
            if (isset($parameters[$key])) {
                $replaces[] = '/' . rawurlencode($parameters[$key]);

                continue;
            }

            if (!$required) {
                $replaces[] = '';

                continue;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_MISSING_ROUTE_PARAMETER, $key));
        }

        return $replaces;
    }

    protected function setHandlers(array $handlers): void
    {
        $this->handlers = $handlers;
    }

    protected function setRouteTree(array $routeTree): void
    {
        $this->routeTree = $routeTree;
    }

    protected function setRouteUris(array $routeUris): void
    {
        $this->routeUris = $routeUris;
    }
}
