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
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Http;

abstract class AbstractHttpHub extends Resource implements NodeInterface
{
    protected const
        NOT_FOUND_CONTENT = 'The requested URL was not found.',
        NOT_FOUND_CONTENT_TYPE = 'text/html';

    /**
     * @inheritDoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo instanceof HttpCargoInterface && $cargo->getStatusCode() === Http::STATUS_CONTINUE_100) {
            return $this->handleHttpCargo($cargo);
        }

        return $cargo;
    }

    /**
     * Handle HTTP cargo
     *
     * @param HttpCargoInterface $cargo
     * @return CargoInterface
     */
    abstract protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface;

    /**
     * Reply not found
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    protected function replyNotFound(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $cargo
            ->setStatusCode(Http::STATUS_NOT_FOUND_404)
            ->setContent(static::NOT_FOUND_CONTENT)
            ->setContentType(static::NOT_FOUND_CONTENT_TYPE);
    }
}
