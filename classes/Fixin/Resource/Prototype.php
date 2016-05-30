<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

abstract class Prototype extends Resource implements PrototypeInterface {

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\PrototypeInterface::withOptions($options)
     */
    public function withOptions(array $options): PrototypeInterface {
        return (clone $this)->configureWithOptions($options);
    }
}