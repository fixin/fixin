<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Base\Json;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;

class JsonToArray extends Resource implements NodeInterface
{
    protected const
        ALLOWED_TYPES = ['application/json', 'application/jsonml+json'];

    public function handle(CargoInterface $cargo): CargoInterface
    {
        $content = $cargo->getContent();

        if (!is_array($content) && in_array($cargo->getContentType(), static::ALLOWED_TYPES)) {
            try {
                $content = $this->resourceManager->get('Base\Json\Json', Json\JsonInterface::class)->decode($content);
                $cargo->setContent(is_array($content) ? $content : null);
            }
            catch (Json\Exception\RuntimeException $e) {
                $cargo->setContent(null);
            }
        }

        return $cargo;
    }
}
