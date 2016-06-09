<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Exception\InvalidArgumentException;
use Fixin\Exception\RuntimeException;
use Fixin\Resource\Resource;
use Fixin\Delivery\Cargo\CargoHandlerInterface;

class HttpRouterHub extends HttpHub {

    const EXCEPTION_INVALID_HANDLER = "Invalid handler '%s'";
    const EXCEPTION_MISSING_ROUTE_PARAMETER = "Missing route parameter '%s'";
    const EXCEPTION_NO_ROUTE_SET = "No route set";
    const EXCEPTION_UNKNOWN_ROUTE = "Unknown route '%s'";

    const KEY_ANY_PARAMETER = ':';
    const KEY_HANDLER = 'handler';
    const KEY_PARAMETERS = 'parameters';
    const KEY_PATTERN_PARAMETER = '?';
    const KEY_URI = 'uri';

    const OPTION_HANDLERS = 'handlers';
    const OPTION_ROUTE_TREE = 'routeTree';
    const OPTION_ROUTE_URIS = 'routeUris';

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @var array
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
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (empty($this->routeTree)) {
            throw new RuntimeException(static::EXCEPTION_NO_ROUTE_SET);
        }

        return $this;
    }

    /**
     * Find handler for segments
     *
     * @param array $segments
     * @param array $node
     * @param array $parameters
     * @return array|null
     */
    protected function findHandler(array $segments, array $node, array $parameters) {
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
     *
     * @param array $segments
     * @param array $node
     * @param array $parameters
     * @param string $segment
     * @return array|NULL
     */
    protected function findHandlerParameter(array $segments, array $node, array $parameters, string $segment) {
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
     *
     * @param array $segments
     * @param array $node
     * @param array $parameters
     * @param string $segment
     * @return array|null
     */
    protected function findHandlerPatternParameter(array $segments, array $node, array $parameters, string $segment) {
        foreach ($node[static::KEY_PATTERN_PARAMETER] as $pattern => $route) {
            if (preg_match("/^$pattern\$/", $segment) && false !== $result = $this->findHandler($segments, $route, $parameters)) {
                return $result;
            }
        }
    }

    /**
     * Get handler instance by name
     *
     * @param string $name
     * @throws InvalidArgumentException
     * @return CargoHandlerInterface
     */
    protected function getHandler(string $name): CargoHandlerInterface {
        if (isset($this->loadedHandlers[$name])) {
            return $this->loadedHandlers[$name];
        }

        $handler = $this->handlers[$name];

        if (is_string($handler)) {
            $handler = $this->container->get($handler);
        }

        if ($handler instanceof CargoHandlerInterface) {
            $this->loadedHandlers[$name] = $handler;

            return $handler;
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_HANDLER, $name));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Node\HttpHub::handleHttpCargo($cargo)
     */
    public function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface {
        $segments = explode('/', trim($cargo->getRequestUri()->getPath(), '/'));
        $count = count($segments);

        if (isset($this->routeTree[$count]) && false !== $found = $this->findHandler($segments, $this->routeTree[$count], [])) {
            $cargo->getRequestParameters()->setFrom($found[static::KEY_PARAMETERS]);

            return $this->getHandler($found[static::KEY_HANDLER])->handle($cargo);
        }

        return $cargo;
    }

    /**
     * Build route URI
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public function route(string $name, array $parameters): string {
        if (isset($this->routeUris[$name])) {
            return vsprintf($this->routeUris[$name][static::KEY_URI], $this->routeParameters($name, $parameters));
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_UNKNOWN_ROUTE, $name));
    }

    /**
     * Build route parameter array
     *
     * @param string $name
     * @param array $parameters
     * @throws InvalidArgumentException
     * @return array
     */
    protected function routeParameters(string $name, array $parameters): array {
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

            throw new InvalidArgumentException(sprintf(static::EXCEPTION_MISSING_ROUTE_PARAMETER, $key));
        }

        return $replaces;
    }

    /**
     * Set handlers
     *
     * @param array $handlers
     */
    protected function setHandlers(array $handlers) {
        $this->handlers = $handlers;
    }

    /**
     * Set route tree
     *
     * @param array $routeTree
     */
    protected function setRouteTree(array $routeTree) {
        $this->routeTree = $routeTree;
    }

    /**
     * Set URIs
     *
     * @param array $routeUris
     */
    protected function setRouteUris(array $routeUris) {
        $this->routeUris = $routeUris;
    }
}