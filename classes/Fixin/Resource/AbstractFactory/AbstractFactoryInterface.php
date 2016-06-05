<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\ResourceInterface;

interface AbstractFactoryInterface extends ResourceInterface {

    /**
     * Produce resource
     *
     * @param array $options
     * @param string $name
     * @return object|null
     */
    public function __invoke(array $options = null, string $name = null);

    /**
     * Determine if we can produce a resource by name
     *
     * @param string $name
     * @return bool
     */
    public function canProduce(string $name): bool;
}