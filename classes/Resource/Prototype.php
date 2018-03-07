<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

abstract class Prototype extends Managed implements PrototypeInterface
{
    /**
     * @inheritDoc
     */
    public function withOptions(array $options): PrototypeInterface
    {
        return (clone $this)
            ->configureWithOptions($options)
            ->testManagedConfiguration();
    }

    /**
     * @inheritDoc
     */
    public function withResourceManager(ResourceManagerInterface $resourceManager): PrototypeInterface
    {
        $clone = clone $this;
        $clone->resourceManager = $resourceManager;

        return $clone;
    }
}