<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Route;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Node\NodeInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Types;

class Route extends Resource implements RouteInterface
{
    protected const
        INVALID_NODE_EXCEPTION = "Invalid node '%s'",
        THIS_SETS = [
            self::NODES => Types::ARRAY
        ];

    /**
     * @var NodeInterface[]
     */
    protected $loadedNodes = [];

    /**
     * @var string[]|NodeInterface[]
     */
    protected $nodes = [];

    /**
     * Get node instance for key
     *
     * @param $key
     * @return NodeInterface
     * @throws Exception\InvalidArgumentException
     */
    protected function getNode($key): NodeInterface
    {
        if (isset($this->loadedNodes[$key])) {
            return $this->loadedNodes[$key];
        }

        $node = $this->nodes[$key];

        if (is_string($node)) {
            return $this->loadedNodes[$key] = $this->resourceManager->get($node, NodeInterface::class);
        }

        if ($node instanceof NodeInterface) {
            return $this->loadedNodes[$key] = $node;
        }

        throw new Exception\InvalidArgumentException(sprintf(static::INVALID_NODE_EXCEPTION, $key));
    }

    /**
     * @inheritdoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        $cargo->setDelivered(false);

        foreach (array_keys($this->nodes) as $key) {
            $cargo = $this->getNode($key)->handle($cargo);

            if ($cargo->isDelivered()) {
                break;
            }
        }

        return $cargo;
    }
}
