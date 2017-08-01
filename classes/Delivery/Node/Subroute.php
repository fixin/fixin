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
    protected const
        THIS_SETS = [
            self::ROUTE => [self::LAZY_LOADING => RouteInterface::class]
        ];

    public const
        ROUTE = 'route';

    /**
     * @var RouteInterface|false|null
     */
    protected $route;

    protected function getRoute(): RouteInterface
    {
        return $this->route ?: $this->loadLazyProperty(static::ROUTE);
    }

    public function handle(CargoInterface $cargo): CargoInterface
    {
        return $this->getRoute()->handle($cargo);
    }
}
