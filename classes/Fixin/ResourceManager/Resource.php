<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

abstract class Resource implements ResourceInterface {

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, $name = null) {
        $this->container = $container;
    }
}