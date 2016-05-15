<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

use Fixin\Support\ContainerInterface;

abstract class Resource implements ResourceInterface {

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(ResourceManagerInterface $container, array $options = []) {
        $this->container = $container;
    }
}