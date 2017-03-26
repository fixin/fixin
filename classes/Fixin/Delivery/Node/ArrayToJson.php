<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;

class ArrayToJson extends Resource implements NodeInterface
{
    protected const
        CONTENT_TYPE = 'application/json';

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if (is_array($cargo->getContent())) {
            $cargo
                ->setContent($this->resourceManager->get('Base\Json\Json')->encode($cargo->getContent()))
                ->setContentType(static::CONTENT_TYPE);
        }

        return $cargo;
    }
}
