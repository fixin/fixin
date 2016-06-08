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
            $uri = $this->routeUris[$name];
            $replaces = [];

            foreach ($uri[static::KEY_PARAMETERS] as $key => $required) {
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

            return vsprintf($uri[static::KEY_URI], $replaces);
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_UNKNOWN_ROUTE, $name));
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