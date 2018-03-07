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
use Fixin\Delivery\Route\RouteInterface;
use Fixin\Resource\Resource;

class Subroute extends Resource implements NodeInterface
{
    public const
        ROUTE = 'route';

    protected const
        THIS_SETS = [
            self::ROUTE => [self::LAZY_LOADING => RouteInterface::class]
        ];

    /**
     * @var RouteInterface|false|null
     */
    protected $route;

    /**
     * Get route
     *
     * @return RouteInterface
     */
    protected function getRoute(): RouteInterface
    {
        return $this->route ?: $this->loadLazyProperty(static::ROUTE);
    }

    /**
     * @inheritDoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        return $this->getRoute()->handle($cargo);
    }
}
