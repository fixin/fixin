<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Dispatcher;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\ResourceManager\{Resource, ResourceManagerInterface};
use Fixin\Delivery\Node\NodeInterface;

class Dispatcher extends Resource implements DispatcherInterface {

    const EXCEPTION_INVALID_NODE = "Invalid node resource '%s'";

    /**
     * @var NodeInterface[]
     */
    protected $nodes = [];

    /**
     * Setup nodes
     *
     * @param array $nodes
     * @throws InvalidArgumentException
     */
    protected function _setupNodes(array $nodes) {
        foreach ($nodes as $key => $node) {
            $node = $this->container->get($node);

            if (!$node instanceof NodeInterface) {
                throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_NODE, $key));
            }

            $this->nodes[] = $node;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Dispatcher\DispatcherInterface::dispatch()
     */
    public function dispatch(CargoInterface $cargo) {
        $cargo->setDelivered(false);
        $plan = $this->nodes;

        while (!empty($plan)) {
            $cargo = array_shift($plan)->handle($cargo);

            if ($cargo->isDelivered()) {
                break;
            }
        }

        return $cargo;
    }
}