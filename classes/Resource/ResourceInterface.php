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
    /**
     * ResourceInterface constructor.
     *
     * @param ResourceManagerInterface $resourceManager
     * @param array $options
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options);
}