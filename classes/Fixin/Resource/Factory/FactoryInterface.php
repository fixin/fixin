<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\Factory;

use Fixin\Resource\ResourceManagerInterface;

interface FactoryInterface
{
    public function __construct(ResourceManagerInterface $container);

    /**
     * Produce resource
     */
    public function __invoke(array $options = null, string $name = null);
}
