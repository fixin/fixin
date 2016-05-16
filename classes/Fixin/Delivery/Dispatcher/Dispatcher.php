<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Dispatcher;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\ResourceManager\Resource;
use Fixin\ResourceManager\ResourceManagerInterface;

class Dispatcher extends Resource implements DispatcherInterface {

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     */
    public function __construct(ResourceManagerInterface $container, array $options = []) {
        parent::__construct($container, $options);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Dispatcher\DispatcherInterface::dispatch()
     */
    public function dispatch(CargoInterface $cargo) {
        return $cargo;
    }
}