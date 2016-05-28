<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Route;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Node\NodeInterface;
use Fixin\ResourceManager\Resource;

class Route extends Resource implements RouteInterface {

    const EXCEPTION_INVALID_NODE = "Invalid node resource '%s'";

    const KEY_NODES = 'nodes';

    /**
     * @var NodeInterface[]
     */
    protected $nodes = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Route\RouteInterface::dispatch()
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

    /**
     * Set nodes
     *
     * @param array $nodes
     * @throws InvalidArgumentException
     */
    protected function setNodes(array $nodes) {
        $this->nodes = [];

        foreach ($nodes as $key => $node) {
            $node = $this->container->get($node);

            if (!$node instanceof NodeInterface) {
                throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_NODE, $key));
            }

            $this->nodes[] = $node;
        }
    }
}