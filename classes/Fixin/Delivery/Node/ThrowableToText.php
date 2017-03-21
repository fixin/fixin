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
use Fixin\Support\Ground;
use Throwable;

class ThrowableToText extends Resource implements NodeInterface
{
    protected const
        CONTENT_TYPE = 'text/html';

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo->getContent() instanceof Throwable) {
            $cargo
                ->setContent(Ground::toDebugText(htmlspecialchars($cargo->getContent())))
                ->setContentType(static::CONTENT_TYPE);
        }

        return $cargo;
    }
}
