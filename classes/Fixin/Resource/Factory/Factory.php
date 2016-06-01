<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\Factory;

use Fixin\Resource\ResourceManagerInterface;

abstract class Factory implements FactoryInterface {

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    /**
     * @param ResourceManagerInterface $container
     */
    public function __construct(ResourceManagerInterface $container) {
        $this->container = $container;
    }
}