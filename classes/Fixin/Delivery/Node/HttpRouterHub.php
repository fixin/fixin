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

class HttpRouterHub extends HttpHub {

    const EXCEPTION_NO_ROUTE_SET = "No route set";

    /**
     * @var array
     */
    protected $patterns;

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
     * Set global patterns
     *
     * @param array $patterns
     */
    protected function setPatterns(array $patterns) {

    }

    /**
     * Set observerd routes
     *
     * @param array $routes
     */
    protected function setRoutes(array $routes) {

    }
}