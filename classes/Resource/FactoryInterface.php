<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface FactoryInterface
{
    /**
     * Produce resource
     *
     * @return object|null
     */
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null, string $name = null);
}