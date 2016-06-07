<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;

class ThrowableToHtml extends Node {

    const CONTENT_TYPE = 'text/html';

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if ($cargo->getContent() instanceof \Throwable) {
            $cargo
            ->setContent('<pre>' . htmlspecialchars($cargo->getContent()) . '</pre>')
            ->setContentType(static::CONTENT_TYPE);
        }

        return $cargo;
    }
}