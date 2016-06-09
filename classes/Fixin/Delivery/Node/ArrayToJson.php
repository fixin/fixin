<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;

class ArrayToJson extends Resource implements NodeInterface {

    const CONTENT_TYPE = 'application/json';

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if (is_array($cargo->getContent())) {
            $cargo
            ->setContent($this->container->get('Base\Json\Json')->encode($cargo->getContent()))
            ->setContentType(static::CONTENT_TYPE);
        }

        return $cargo;
    }
}