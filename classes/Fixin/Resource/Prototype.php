<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

abstract class Prototype extends Resource implements PrototypeInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\PrototypeInterface::withOptions($options)
     */
    public function withOptions(array $options): PrototypeInterface {
        return (clone $this)->configureWithOptions($options);
    }
}