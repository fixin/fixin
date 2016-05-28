<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;

class JsonToArray extends Node implements NodeInterface {

    const JSON_TYPES = ['application/json', 'application/jsonml+json'];

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Node\NodeInterface::handle()
     */
    public function handle(CargoInterface $cargo) {
        if (in_array($cargo->getContentType(), static::JSON_TYPES)) {
            $cargo->setContent($this->container->get('Base\Json\Json')->decode($cargo->getContent()));
        }

        return $cargo;
    }
}