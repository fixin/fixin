<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface PrototypeInterface extends ManagedInterface
{
    /**
     * Cloned instance with changed options
     *
     * @return static
     */
    public function withOptions(array $options): PrototypeInterface;

    /**
     * Cloned instance with replaced Resource Manager
     *
     * @return static
     */
    public function withResourceManager(ResourceManagerInterface $resourceManager): PrototypeInterface;
}