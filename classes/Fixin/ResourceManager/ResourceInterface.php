<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

interface ResourceInterface {

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null);
}