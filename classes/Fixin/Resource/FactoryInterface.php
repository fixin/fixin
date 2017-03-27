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
     * @param ResourceManagerInterface $resourceManager
     */
    public function __construct(ResourceManagerInterface $resourceManager);

    /**
     * Produce resource
     *
     * @return object|null
     */
    public function __invoke(array $options = null, string $name = null);
}
