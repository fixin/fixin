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
use Fixin\Support\Http;

abstract class HttpRestfulController extends AbstractController
{
    protected const
        CONTENT_METHOD_NOT_ALLOWED = [
            'content' => 'Method Not Allowed'
        ],
        CONTENT_TYPE = 'application/json',
        METHOD_MAP = [
            Http::DELETE_METHOD => 'deleteMethod',
            Http::GET_METHOD => 'getMethod',
            Http::HEAD_METHOD => 'headMethod',
            Http::OPTIONS_METHOD => 'optionsMethod',
            Http::PATCH_METHOD => 'patchMethod',
            Http::POST_METHOD => 'postMethod',
            Http::PUT_METHOD => 'putMethod'
        ];

    /**
     * Delete a resource
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function deleteMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Retrieve information
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function getMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * @inheritDoc
     */
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
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function headMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Allowed methods
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function optionsMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Patch request
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function patchMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Create or replace resource (non-idempotent)
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function postMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Replace or create resource (idempotent)
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    public function putMethod(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $this->replyMethodNotAllowed($cargo);
    }

    /**
     * Reply: Method not allowed
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    protected function replyMethodNotAllowed(HttpCargoInterface $cargo): HttpCargoInterface
    {
        return $cargo
            ->setStatusCode(Http::STATUS_METHOD_NOT_ALLOWED_405)
            ->setContent(static::CONTENT_METHOD_NOT_ALLOWED)
            ->setContentType(static::CONTENT_TYPE);
    }
}
