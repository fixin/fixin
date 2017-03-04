<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;
use Fixin\View\ViewInterface;

class ViewRender extends Resource implements NodeInterface
{
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
