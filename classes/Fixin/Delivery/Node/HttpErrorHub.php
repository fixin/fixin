<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Delivery\Route\RouteInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Http;

class HttpErrorHub extends Resource implements NodeInterface
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_ROUTE
        ],
        THIS_SETS_LAZY = [
            self::OPTION_ROUTE => RouteInterface::class
        ];

    public const
        OPTION_ROUTE = 'route';

    /**
     * @var RouteInterface|false|null
     */
    protected $route;

    protected function getRoute(): RouteInterface
    {
        return $this->route ?: $this->loadLazyProperty(static::OPTION_ROUTE);
    }

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo instanceof HttpCargoInterface) {
            $statusCode = $cargo->getStatusCode();

            if ($statusCode >= Http::STATUS_BAD_REQUEST_400 && $statusCode <= Http::STATUS_LAST_POSSIBLE_ERROR) {
                return $this->getRoute()
                    ->handle($cargo)
                    ->setDelivered(true);
            }
        }

        return $cargo;
    }
}
