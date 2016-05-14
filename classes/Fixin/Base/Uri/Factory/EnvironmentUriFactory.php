<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri\Factory;

use Fixin\Base\Uri\Uri;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\ContainerInterface;

class EnvironmentUriFactory implements FactoryInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, string $name = null) {
        $uri = new Uri();
        $uri->setScheme(($https = $_SERVER['HTTPS'] ?? false) && $https !== 'off' ? 'https' : 'http')
        ->setHost($_SERVER['HTTP_HOST'])
        ->setPort($_SERVER['SERVER_PORT'])
        ->setPath(($index = strpos($path = $this->getUriString(), '?')) ? substr($path, 0, $index) : $path)
        ->setQuery($_SERVER['QUERY_STRING']);

        return $uri;
    }

    /**
     * Get URI string
     *
     * @throws InvalidConfigException
     * @return string
     */
    protected function getUriString(): string {
        if ($requestUri = $_SERVER['HTTP_X_REWRITE_URL'] ?? false) {
            return $requestUri;
        }

        if ($requestUri = $_SERVER['REQUEST_URI']) {
            if ($requestUri[0] === '/') {
                return $requestUri;
            }

            return preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
        }

        if ($requestUri = $_SERVER['ORIG_PATH_INFO'] ?? false) {
            return $requestUri;
        }

        throw new InvalidConfigException('Can\'t determine the request URI');
    }
}