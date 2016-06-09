<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Exception\RuntimeException;
use Fixin\Resource\Resource;
use Fixin\Exception\InvalidArgumentException;
use Fixin\Delivery\Cargo\CargoHandlerInterface;

class HttpRouterHub extends HttpHub {

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
     * {@inheritDoc}
     * @see \Fixin\Delivery\Node\HttpHub::handleHttpCargo($cargo)
     */
    public function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface {
        $segments = explode('/', trim($cargo->getRequestUri()->getPath(), '/'));
        $count = count($segments);

        if (isset($this->routeTree[$count])) {
            $handlerFound = false;

            return $this->toHandler($cargo, $segments, $this->routeTree[$count], [], $handlerFound);
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

    /**
     * Route to handler
     *
     * @param HttpCargoInterface $cargo
     * @param array $segments
     * @param array $node
     * @param array $parameters
     * @param bool $handlerFound
     * @return CargoInterface
     */
    protected function toHandler(HttpCargoInterface $cargo, array $segments, array $node, array $parameters, bool &$handlerFound): CargoInterface {
        // Handler
        if (empty($segments)) {
            $handlerFound = true;
            //             $cargo->setRequestParameters(array_combine($node[static::KEY_PARAMETERS], $parameters) + $cargo->getRequestParameters());

            return $this->getHandler($node[static::KEY_HANDLER]($cargo));
        }

        // Next segment
        $segment = array_shift($segments);

        // Normal segment
        if (isset($node[$segment])) {
            return $this->toHandler($cargo, $segments, $node[$segment], $parameters, $handlerFound);
        }

        $segment = rawurldecode($segment);
        $parameters[] = $segment;

        // Pattern parameter
        if (isset($node[static::KEY_PATTERN_PARAMETER])) {
            foreach ($node[static::KEY_PATTERN_PARAMETER] as $pattern => $route) {
                if (preg_match("/^$pattern\$/", $segment)) {
                    $cargo = $this->toHandler($cargo, $segments, $route, $parameters, $handlerFound);

                    if ($handlerFound) {
                        return $cargo;
                    }
                }
            }

            return $cargo;
        }

        // Any parameter
        if (isset($node[static::KEY_ANY_PARAMETER])) {

            return $this->toHandler($cargo, $segments, $node[static::KEY_ANY_PARAMETER], $parameters, $handlerFound);
        }

        return $cargo;
    }
}