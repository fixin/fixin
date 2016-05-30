<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

interface PrototypeInterface extends ResourceInterface {

    /**
     * New instance with changed options
     *
     * @param array $options
     * @return self
     */
    public function withOptions(array $options): PrototypeInterface;
}