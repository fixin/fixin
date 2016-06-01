<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\Factory;

use Fixin\Resource\ResourceManagerInterface;

interface FactoryInterface {

    /**
     * @param ResourceManagerInterface $container
     */
    public function __construct(ResourceManagerInterface $container);

    /**
     * Produce resource
     *
     * @param array $options
     * @param string $name
     * @return mixed
     */
    public function __invoke(array $options = null, string $name = null);
}