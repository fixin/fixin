<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Delivery\Route\RouteInterface;
use Fixin\Exception\RuntimeException;
use Fixin\Resource\Resource;
use Fixin\Support\Http;

class HttpErrorHub extends Resource implements NodeInterface {

    const EXCEPTION_ROUTE_NOT_SET = "Route not set";

    /**
     * @var RouteInterface|false|null
     */
    protected $route;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (!isset($this->route)) {
            throw new RuntimeException(static::EXCEPTION_ROUTE_NOT_SET);
        }

        return $this;
    }

    /**
     * Get route instance
     *
     * @return RouteInterface
     */
    protected function getRoute(): RouteInterface {
        return $this->route ?: $this->loadLazyProperty('route');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if ($cargo instanceof HttpCargoInterface) {
            $statusCode = $cargo->getStatusCode();

            if ($statusCode >= Http::STATUS_BAD_REQUEST_400 && $statusCode <= Http::STATUS_LAST_POSSIBLE_ERROR) {
                return $this->getRoute()->handle($cargo)->setDelivered(true);
            }
        }

        return $cargo;
    }

    /**
     * Set route
     *
     * @param string|RouteInterface $route
     */
    protected function setRoute($route) {
        $this->setLazyLoadingProperty('route', RouteInterface::class, $route);
    }
}