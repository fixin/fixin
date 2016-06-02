<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;

class ArrayToJson extends Node {

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if (is_array($cargo->getContent())) {
            $cargo
            ->setContent($this->container->get('Base\Json\Json')->encode($cargo->getContent()))
            ->setContentType('application/json');
        }

        return $cargo;
    }
}