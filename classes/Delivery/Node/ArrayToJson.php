<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Base\Json\JsonInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;

class ArrayToJson extends Resource implements NodeInterface
{
    protected const
        CONTENT_TYPE = 'application/json';

    /**
     * @inheritDoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        if (is_array($cargo->getContent())) {
            $cargo
                ->setContent($this->resourceManager->get('*\Base\Json\Json', JsonInterface::class)->encode($cargo->getContent()))
                ->setContentType(static::CONTENT_TYPE);
        }

        return $cargo;
    }
}
