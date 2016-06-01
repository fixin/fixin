<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\Factory;

interface FactoryInterface {

    /**
     * Produce resource
     *
     * @param array $options
     * @param string $name
     * @return mixed
     */
    public function __invoke(array $options = null, string $name = null);
}