<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface FactoryInterface extends ResourceInterface
{
    /**
     * Produce resource
     *
     * @return object|null
     */
    public function __invoke(array $options = null, string $name = null);
}
