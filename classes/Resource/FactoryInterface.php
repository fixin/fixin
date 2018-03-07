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
     * @param ResourceManagerInterface $resourceManager
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null);
}