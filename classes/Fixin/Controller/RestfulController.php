<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Controller;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Http;

abstract class RestfulController extends Resource implements ControllerInterface
{
    protected const
        CONTENT_METHOD_NOT_ALLOWED = [
            'content' => 'Method Not Allowed'
        ],
        CONTENT_TYPE = 'application/json',
        METHOD_MAP = [
            Http::METHOD_DELETE => 'delete',
            Http::METHOD_GET => 'get',
            Http::METHOD_HEAD => 'head',
            Http::METHOD_OPTIONS => 'options',
            Http::METHOD_PATCH => 'patch',
            Http::METHOD_POST => 'post',
            Http::METHOD_PUT => 'put'
        ];

    /**
     * Delete a resource
     */
    public function delete(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Retrieve information
     */
    public function get(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo instanceof HttpCargoInterface) {
            $method = $cargo->getRequestMethod();

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
    public function head(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Allowed methods
     */
    public function options(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Patch request
     */
    public function patch(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Create or replace resource (non-idempotent)
     */
    public function post(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Replace or create resource (idempotent)
     */
    public function put(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Reply: Method not allowed
     */
    protected function replyMethodNotAllowed(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $cargo
            ->setContent(static::CONTENT_METHOD_NOT_ALLOWED)
            ->setContentType(static::CONTENT_TYPE)
            ->setStatusCode(Http::STATUS_METHOD_NOT_ALLOWED_405);
    }
}
