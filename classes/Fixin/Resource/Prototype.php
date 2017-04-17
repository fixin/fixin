<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

abstract class Prototype extends Managed implements PrototypeInterface
{
    /**
     * @return static
     */
    public function withOptions(array $options)
    {
        return (clone $this)
            ->configureWithOptions($options)
            ->configurationTest(get_class($this));
    }

    /**
     * @return static
     */
    public function withResourceManager(ResourceManagerInterface $resourceManager): ResourceInterface
    {
        $clone = clone $this;
        $clone->resourceManager = $resourceManager;

        return $clone;
    }
}
