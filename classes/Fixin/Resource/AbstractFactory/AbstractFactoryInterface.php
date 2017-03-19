<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\FactoryInterface;

interface AbstractFactoryInterface extends FactoryInterface
{
    /**
     * Determine if we can produce a resource by name
     */
    public function canProduce(string $name): bool;
}
