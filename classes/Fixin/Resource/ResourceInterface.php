<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

interface ResourceInterface
{
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null);
}
