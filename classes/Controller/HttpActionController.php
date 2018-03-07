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
use Fixin\Support\Strings;

abstract class HttpActionController extends AbstractController
{
    protected const
        ACTION_PARAMETER = 'action',
        CONTENT_TYPE = 'text/html',
        DEFAULT_ACTION = 'index',
        INVALID_RETURN_TYPE_EXCEPTION = "Method '%s' returned invalid type",
        NOT_FOUND_CONTENT = 'The requested URL was not found.',
        NOT_FOUND_CONTENT_TYPE = 'text/html';

    /**
     * Get method name of action if exists and it is accessible
     *
     * @param string $action
     * @return string
     * @throws \ReflectionException
     */
    protected function getActionMethodName(string $action): string
    {
        $method = Strings::toMethodName($action) . 'Action';

        // Checking method
        if (method_exists($this, $method)) {
            $reflection = new \ReflectionMethod($this, $method);

            // Only public methods
            if ($reflection->isPublic() && $reflection->getName() === $method) {
                return $method;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        if ($cargo instanceof HttpCargoInterface) {
            if ($method = $this->getActionMethodName($cargo->getParameters()->get(static::ACTION_PARAMETER, static::DEFAULT_ACTION))) {
                return $this->handleMethod($cargo, $method);
            }

            return $this->replyNotFound($cargo);
        }

        return $cargo;
    }

    /**
     * Call method and handle different answer types
     *
     * @param HttpCargoInterface $cargo
     * @param string $method
     * @return CargoInterface
     */
    protected function handleMethod(HttpCargoInterface $cargo, string $method): CargoInterface
    {
        // Call method
        $answer = $this->$method($cargo
            ->setStatusCode(Http::STATUS_OK_200)
            ->setContentType(static::CONTENT_TYPE)
        );

        // Cargo
        if ($answer instanceof CargoInterface) {
            return $answer;
        }

        // False
        if ($answer === false) {
            return $this->replyNotFound($cargo);
        }

        throw new Exception\InvalidReturnTypeException(sprintf(static::INVALID_RETURN_TYPE_EXCEPTION, $method));
    }

    /**
     * Reply Not Found
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
