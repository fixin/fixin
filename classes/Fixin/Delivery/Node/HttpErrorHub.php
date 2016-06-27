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
use Fixin\Resource\Resource;
use Fixin\Support\Http;

class HttpErrorHub extends Resource implements NodeInterface {

    const OPTION_ROUTE = 'route';
    const THIS_REQUIRES = [
        self::OPTION_ROUTE => self::TYPE_INSTANCE
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_ROUTE => RouteInterface::class
    ];

    /**
     * @var RouteInterface|false|null
     */
    protected $route;

    /**
     * Get route instance
     *
     * @return RouteInterface
     */
    protected function getRoute(): RouteInterface {
        return $this->route ?: $this->loadLazyProperty(static::OPTION_ROUTE);
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
}