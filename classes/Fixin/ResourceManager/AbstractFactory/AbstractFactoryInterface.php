<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\AbstractFactory;

use Fixin\ResourceManager\Factory\FactoryInterface;

interface AbstractFactoryInterface extends FactoryInterface {

    /**
     * Determine if we can produce a resource by name
     *
     * @param string $name
     * @return bool
     */
    public function canProduce(string $name): bool;
}