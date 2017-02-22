<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Ground;

class ThrowableToText extends Resource implements NodeInterface
{
    protected const
        CONTENT_TYPE = 'text/html';

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo->getContent() instanceof \Throwable) {
            $cargo
            ->setContent(Ground::debugText(htmlspecialchars($cargo->getContent())))
            ->setContentType(static::CONTENT_TYPE);
        }

        return $cargo;
    }
}
