<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Controller;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Exception\UnexpectedValueException;
use Fixin\Resource\Resource;
use Fixin\Support\Http;
use Fixin\Support\Strings;

abstract class ActionController extends Resource implements ControllerInterface {

    const ACTION_PARAMETER = 'action';
    const DEFAULT_ACTION = 'index';
    const EXCEPTION_INVALID_RETURN_TYPE = "Method '%s' returned invalid type";

    /**
     * Get method name of action if exists and it is accessible
     *
     * @param string $action
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getActionMethodName(string $action): string {
        $method = Strings::methodName($action);

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
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if ($cargo instanceof HttpCargoInterface) {
            if ($method = $this->getActionMethodName($cargo->getRequestParameter(static::ACTION_PARAMETER, static::DEFAULT_ACTION))) {
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
     * @throws UnexpectedValueException
     * @return CargoInterface
     */
    protected function handleMethod(HttpCargoInterface $cargo, string $method): CargoInterface {
        // Call method
        $answer = $this->$method($cargo->setStatusCode(Http::STATUS_OK_200));

        // Cargo
        if ($answer instanceof CargoInterface) {
            return $answer;
        }

        // False
        if ($answer === false) {
            return $this->replyNotFound($cargo);
        }

        throw new UnexpectedValueException(sprintf(static::EXCEPTION_INVALID_RETURN_TYPE, $method));
    }

    /**
     * Reply: Not Found (404)
     *
     * @param HttpCargoInterface $cargo
     * @return HttpCargoInterface
     */
    protected function replyNotFound(HttpCargoInterface $cargo): HttpCargoInterface {
        return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
    }
}