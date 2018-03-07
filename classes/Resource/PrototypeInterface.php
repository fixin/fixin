<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface PrototypeInterface extends ResourceInterface
{
    /**
     * Clone with options
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options): PrototypeInterface;

    /**
     * Clone with resource manager
     *
     * @param ResourceManagerInterface $resourceManager
     * @return static
     */
    public function withResourceManager(ResourceManagerInterface $resourceManager): PrototypeInterface;
}