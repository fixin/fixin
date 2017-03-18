<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Controller;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Http;

abstract class HttpRestfulController extends Resource implements ControllerInterface
{
    protected const
        CONTENT_METHOD_NOT_ALLOWED = [
            'content' => 'Method Not Allowed'
        ],
        CONTENT_TYPE = 'application/json',
        METHOD_MAP = [
            Http::METHOD_DELETE => 'deleteMethod',
            Http::METHOD_GET => 'getMethod',
            Http::METHOD_HEAD => 'headMethod',
            Http::METHOD_OPTIONS => 'optionsMethod',
            Http::METHOD_PATCH => 'patchMethod',
            Http::METHOD_POST => 'postMethod',
            Http::METHOD_PUT => 'putMethod'
        ];

    /**
     * Delete a resource
     */
    public function deleteMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Retrieve information
     */
    public function getMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo instanceof HttpCargoInterface) {
            $method = $cargo->getMethod();

            if (isset(static::METHOD_MAP[$method])) {
                return $this->{static::METHOD_MAP[$method]}($cargo->setStatusCode(Http::STATUS_OK_200));
            }

            return $this->replyMethodNotAllowed($cargo);
        }

        return $cargo;
    }

    /**
     * Metadata of the resource
     */
    public function headMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Allowed methods
     */
    public function optionsMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Patch request
     */
    public function patchMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Create or replace resource (non-idempotent)
     */
    public function postMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Replace or create resource (idempotent)
     */
    public function putMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Reply: Method not allowed
     */
    protected function replyMethodNotAllowed(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $cargo
            ->setStatusCode(Http::STATUS_METHOD_NOT_ALLOWED_405)
            ->setContent(static::CONTENT_METHOD_NOT_ALLOWED)
            ->setContentType(static::CONTENT_TYPE);
    }
}
