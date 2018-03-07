<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Uri\Factory;

use Fixin\Base\Uri\UriInterface;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;

class EnvironmentUriFactory implements FactoryInterface
{
    protected const
        CAN_T_DETERMINE_EXCEPTION = 'Can\'t determine the request URI';

    /**
     * Produce URI
     *
     * @param ResourceManagerInterface $resourceManager
     * @param array|null $options
     * @return UriInterface
     */
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null): UriInterface
    {
        return $resourceManager->clone('*\Base\Uri\Uri', UriInterface::class, [
            UriInterface::SCHEME => ($https = $_SERVER['HTTPS'] ?? false) && $https !== 'off' ? 'https' : 'http',
            UriInterface::HOST => $_SERVER['HTTP_HOST'],
            UriInterface::PORT => (int) $_SERVER['SERVER_PORT'],
            UriInterface::PATH => $this->getPath(),
            UriInterface::QUERY => $_SERVER['QUERY_STRING']
        ]);
    }

    /**
     * Get path
     *
     * @return string
     */
    protected function getPath(): string
    {
        $uri = $_SERVER['HTTP_X_REWRITE_URL'] ?? $_SERVER['REQUEST_URI'] ?? $_SERVER['ORIG_PATH_INFO'] ?? (function () {
            throw new Exception\RuntimeException(static::CAN_T_DETERMINE_EXCEPTION);
        })();

        if (false === $index = strpos($uri, '?')) {
            return $uri;
        }

        return substr($uri, 0, $index);
    }
}
