<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface ManagedInterface
{
    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null);
}