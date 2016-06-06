<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoHandlerInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Exception\RuntimeException;
use Fixin\Support\Http;
use Fixin\Support\Strings;

class HttpClassHub extends HttpHub {

    const CLASS_NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_\\\\]*$/';
    const EXCEPTION_INVALID_CLASS = "Class '%s' is invalid, CargoHandlerInterface required";

    /**
     * @var string
     */
    protected $basePath = '/';

    /**
     * @var string
     */
    protected $classPrefix = '';

    /**
     * @var integer
     */
    protected $depth = 2;

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Node\HttpHub::handleHttpCargo($cargo)
     */
    protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface {
        $path = $cargo->getRequestUri()->getPath();
        $length = strlen($this->basePath);

        if (strncmp($path, $this->basePath, $length) === 0) {
            $path = substr($path, $length);
            if (strlen($path)) {
                return $this->handlePath($cargo, $path);
            }

            return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
        }

        return $cargo;
    }

    /**
     * Handle observed path
     *
     * @param HttpCargoInterface $cargo
     * @param string $path
     * @throws RuntimeException
     * @return HttpCargoInterface
     */
    protected function handlePath(HttpCargoInterface $cargo, string $path): HttpCargoInterface {
        $depth = $this->depth;
        $tags = explode('/', rtrim($path, '/'), $depth + 2);

        if (!isset($tags[$depth + 1])) {
            // Action
            if (isset($tags[$depth])) {
                $cargo->setRequestParameter('action', $tags[$depth]);
            }

            // Name to class
            $name = implode('\\', array_slice($tags, 0, $depth));
            if (preg_match(static::CLASS_NAME_PATTERN, $name)) {
                $fullName = $this->classPrefix . Strings::className($name);

                // Test class
                if ($this->container->has($fullName)) {
                    $instance = $this->container->get($fullName);

                    if ($instance instanceof CargoHandlerInterface) {
                        return $instance->handle($cargo);
                    }

                    throw new RuntimeException(sprintf(static::EXCEPTION_INVALID_CLASS, get_class($instance)));
                }
            }
        }

        return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
    }

    /**
     * Set base path
     *
     * @param string $basePath
     */
    protected function setBasePath(string $basePath) {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    /**
     * Set class prefix
     *
     * @param string $classPrefix
     */
    protected function setClassPrefix(string $classPrefix) {
        $this->classPrefix = trim($classPrefix, '\\') . '\\';
    }

    /**
     * Set depth
     *
     * @param int $depth
     */
    protected function setDepth(int $depth) {
        $this->depth = $depth;
    }
}