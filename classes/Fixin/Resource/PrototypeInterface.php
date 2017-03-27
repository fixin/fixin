<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

interface PrototypeInterface extends ResourceInterface
{
    /**
     * Cloned instance with changed options
     *
     * @return static
     */
    public function withOptions(array $options);
}
