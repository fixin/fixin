<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;

class JsonToArray extends Resource implements NodeInterface {

    const ALLOWED_TYPES = ['application/json', 'application/jsonml+json'];

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if (in_array($cargo->getContentType(), static::ALLOWED_TYPES)) {
            $cargo->setContent($this->container->get('Base\Json\Json')->decode($cargo->getContent()));
        }

        return $cargo;
    }
}