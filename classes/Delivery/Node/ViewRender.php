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
use Fixin\View\ViewInterface;

class ViewRender extends Resource implements NodeInterface
{
    /**
     * @inheritDoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        $content = $cargo->getContent();

        if ($content instanceof ViewInterface) {
            $cargo
                ->setContent($content->render())
                ->setContentType($content->getContentType());
        }

        return $cargo;
    }
}
