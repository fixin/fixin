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
use Fixin\Delivery\Node\NodeInterface;

class Dispatcher extends Resource implements DispatcherInterface {

    const NODES_KEY = 'nodes';

    /**
     * @var NodeInterface[]
     */
    protected $nodes = [];

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     */
    public function __construct(ResourceManagerInterface $container, array $options = []) {
        parent::__construct($container, $options);

        // Facilities
        if (isset($options[static::NODES_KEY])) {
            $this->setupNodes($options[static::NODES_KEY]);
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

    /**
     * Setup nodes
     *
     * @param array $nodes
     * @throws InvalidParameterException
     */
    protected function setupNodes(array $nodes) {
        foreach ($nodes as $key => $node) {
            $node = $this->container->get($node);

            if (!$node instanceof NodeInterface) {
                throw new InvalidParameterException("Invalid facility resource '$key'");
            }

            $this->nodes[] = $node;
        }
    }
}