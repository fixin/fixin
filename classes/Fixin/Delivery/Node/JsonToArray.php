<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

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
                $content = $this->container->get('Base\Json\Json')->decode($content);
                $cargo->setContent(is_array($content) ? $content : null);
            }
            catch (\Fixin\Base\Json\Exception\RuntimeException $e) {
                $cargo->setContent(null);
            }
        }

        return $cargo;
    }
}
