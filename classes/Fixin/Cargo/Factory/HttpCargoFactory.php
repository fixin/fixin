<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Cargo\Factory;

use Fixin\Base\Uri\Uri;
use Fixin\Cargo\HttpCargo;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\ContainerInterface;
use Fixin\Support\Http;

class HttpCargoFactory implements FactoryInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, string $name = null) {
        $cargo = new HttpCargo();
        $cargo->setRequestProtocolVersion($this->getProtocolVersion())
            ->setRequestMethod($method = $this->getMethod())
            ->setRequestUri($this->getUri())
            ->setRequestParameters($_GET)
            ->setRequestHeaders($this->getHeaders())
            ->setCookies($_COOKIE)
            ->setEnvironmentParameters($_ENV)
            ->setServerParameters($_SERVER);

        // POST
        if ($method === Http::METHOD_POST) {
            $cargo->setContent($this->getPostParameters());
        }

        return $cargo;
    }

    /**
     * Get header values
     *
     * @return array
     */
    protected function getHeaders(): array {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (!strncmp($name, 'HTTP_', 5)) {
                $headers[strtr(ucwords(strtolower(substr($name, 5)), '_'), '_', '-')] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get method
     *
     * @return string
     */
    protected function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get POST parameters
     *
     * @return array
     */
    protected function getPostParameters(): array {
        $post = $_POST;

        // Files
        if ($_FILES) {
            foreach ($_FILES as $key => $file) {
                $post[$key] = $file;
            }
        }

        return $post;
    }

    /**
     * Get protocol version
     *
     * @return string
     */
    protected function getProtocolVersion(): string {
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::PROTOCOL_VERSION_1_0)
            ? Http::PROTOCOL_VERSION_1_0 : Http::PROTOCOL_VERSION_1_1;
    }

    /**
     * Get URI instance
     *
     * @return Uri
     */
    protected function getUri(): Uri {
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
            if ($requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }

            return $requestUri;
        }

        if ($requestUri = $_SERVER['ORIG_PATH_INFO'] ?? false) {
            return $requestUri;
        }

        throw new InvalidConfigException('Can\'t determine the request URI');
    }
}