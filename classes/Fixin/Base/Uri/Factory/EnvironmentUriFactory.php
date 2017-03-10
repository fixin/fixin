<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri\Factory;

use Fixin\Base\Uri\UriInterface;
use Fixin\Resource\Factory\Factory;

class EnvironmentUriFactory extends Factory
{
    protected const
        EXCEPTION_CAN_T_DETERMINE = 'Can\'t determine the request URI';

    public function __invoke(array $options = NULL, string $name = null)
    {
        return $this->container->clone('Base\Uri\Uri', [
            UriInterface::OPTION_SCHEME => ($https = $_SERVER['HTTPS'] ?? false) && $https !== 'off' ? 'https' : 'http',
            UriInterface::OPTION_HOST => $_SERVER['HTTP_HOST'],
            UriInterface::OPTION_PORT => $_SERVER['SERVER_PORT'],
            UriInterface::OPTION_PATH => $this->getPath(),
            UriInterface::OPTION_QUERY => $_SERVER['QUERY_STRING']
        ]);
    }

    protected function getPath(): string
    {
        $uri = $_SERVER['HTTP_X_REWRITE_URL'] ?? $_SERVER['REQUEST_URI'] ?? $_SERVER['ORIG_PATH_INFO'] ?? (function() {
            throw new Exception\RuntimeException(static::EXCEPTION_CAN_T_DETERMINE);
        })();

        if (false === $index = strpos($uri, '?')) {
            return $uri;
        }

        return substr($uri, 0, $index);
    }
}
