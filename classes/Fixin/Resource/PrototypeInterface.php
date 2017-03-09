<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

interface PrototypeInterface extends ResourceInterface
{
    /**
     * Instance with changed options
     */
    public function withOptions(array $options);
}
