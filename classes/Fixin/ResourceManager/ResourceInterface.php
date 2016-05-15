<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

use Fixin\Support\ContainerInterface;

interface ResourceInterface {

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     */
    public function __construct(ResourceManagerInterface $container, array $options = []);
}