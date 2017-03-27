<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface ResourceInterface
{
    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null);

    /**
     * Cloned instance with replaced Resource Manager
     *
     * @return static
     */
    public function withResourceManager(ResourceManagerInterface $resourceManager): ResourceInterface;
}
