<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\Factory;

use Fixin\Resource\ResourceInterface;

interface FactoryInterface extends ResourceInterface {

    /**
     * Produce resource
     *
     * @param array $options
     * @param string $name
     * @return mixed
     */
    public function __invoke(array $options = null, string $name = null);
}