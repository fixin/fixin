<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\Factory\FactoryInterface;

interface AbstractFactoryInterface extends FactoryInterface {

    /**
     * Determine if we can produce a resource by name
     *
     * @param string $name
     * @return bool
     */
    public function canProduce(string $name): bool;
}