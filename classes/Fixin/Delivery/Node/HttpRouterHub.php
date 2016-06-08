<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Resource;

class HttpRouterHub extends HttpHub {

    const EXCEPTION_NO_ROUTE_SET = "No route set";

    const KEY_ANY_PARAMETER = ':';
    const KEY_HANDLER = 'handler';
    const KEY_PARAMETERS = 'parameters';
    const KEY_PATTERN_PARAMETER = '?';

    const OPTION_HANDLERS = 'handlers';
    const OPTION_PARSED_ROUTES = 'parsedRoutes';

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @var array
     */
    protected $routes;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (empty($this->routes)) {
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
     * @param string $routeName
     * @param array $parameters
     * @return string
     */
    public function route(string $routeName, array $parameters): string {

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
     * Set observerd routes
     *
     * @param array $definition
     */
    protected function setParsedRoutes(array $routes) {
        $this->routes = $routes;
    }
}