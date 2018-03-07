<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoHandlerInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Delivery\Node\Exception;
use Fixin\Support\Strings;
use Fixin\Support\Types;

class HttpClassHub extends AbstractHttpHub
{
    public const
        BASE_PATH = 'basePath',
        CLASS_PREFIX = 'classPrefix',
        DEPTH = 'depth';

    protected const
        CLASS_NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_\\\\]*$/',
        THIS_SETS = [
            self::BASE_PATH => self::USING_SETTER,
            self::CLASS_PREFIX => self::USING_SETTER,
            self::DEPTH => Types::INT
        ];

    /**
     * @var string
     */
    protected $basePath = '/';

    /**
     * @var string
     */
    protected $classPrefix = '';

    /**
     * @var int
     */
    protected $depth = 2;

    /**
     * Get handler for given path
     *
     * @param string $name
     * @return CargoHandlerInterface|null
     */
    protected function getHandlerForPath(string $name): ?CargoHandlerInterface
    {
        if (preg_match(static::CLASS_NAME_PATTERN, $name)) {
            $fullName = $this->classPrefix . Strings::toClassName($name);

            // Test class
            if ($this->resourceManager->has($fullName)) {
                return $this->resourceManager->get($fullName, CargoHandlerInterface::class);
            }
        }

        return null;
    }

    /**
     * Handle HTTP cargo
     *
     * @param HttpCargoInterface $cargo
     * @return CargoInterface
     */
    protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface
    {
        $path = $cargo->getUri()->getPath();
        $length = strlen($this->basePath);

        if (strncmp($path, $this->basePath, $length) === 0) {
            $path = substr($path, $length);
            if (strlen($path)) {
                return $this->handlePath($cargo, $path);
            }

            return $this->replyNotFound($cargo);
        }

        return $cargo;
    }

    /**
     * Handle observed path
     *
     * @param HttpCargoInterface $cargo
     * @param string $path
     * @return CargoInterface
     */
    protected function handlePath(HttpCargoInterface $cargo, string $path): CargoInterface
    {
        $depth = $this->depth;
        $tags = explode('/', rtrim($path, '/'), $depth + 2);

        if (!isset($tags[$depth + 1])) {
            // Action
            if (isset($tags[$depth])) {
                $cargo->getParameters()->set('action', $tags[$depth]);
            }

            // Handler
            if ($handler = $this->getHandlerForPath(implode('\\', array_slice($tags, 0, $depth)))) {
                return $handler->handle($cargo);
            }
        }

        return $this->replyNotFound($cargo);
    }

    /**
     * Set base path
     *
     * @param string $basePath
     */
    protected function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    /**
     * Set class prefix
     *
     * @param string $classPrefix
     */
    protected function setClassPrefix(string $classPrefix): void
    {
        $this->classPrefix = trim($classPrefix, '\\') . '\\';
    }
}
