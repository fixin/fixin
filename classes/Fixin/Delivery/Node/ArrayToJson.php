<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;

class ArrayToJson extends Node {

    const JSON_TYPES = ['application/json', 'application/jsonml+json'];

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if (is_array($cargo->getContent()) && !in_array($cargo->getContentType(), static::JSON_TYPES)) {
            $cargo->setContent($this->container->get('Base\Json\Json')->encode($cargo->getContent()));
        }

        return $cargo;
    }
}