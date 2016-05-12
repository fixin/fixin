<?php

namespace Fixin\Cargo\Factory;

use Fixin\Cargo\HttpCargo;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\ContainerInterface;

class HttpCargoFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, string $name = null) {
        $cargo = new HttpCargo();

        return $cargo;
    }
}