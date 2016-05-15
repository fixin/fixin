<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Delivery\Cargo\HttpCargo;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\ResourceManager\ResourceManagerInterface;
use Fixin\Support\Http;

class HttpCargoFactory implements FactoryInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(ResourceManagerInterface $container, string $name = null) {
        $cargo = new HttpCargo();
        $cargo->setRequestProtocolVersion($this->getProtocolVersion())
            ->setRequestMethod($method = $this->getMethod())
            ->setRequestUri($container->clonePrototype('requestUri'))
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get POST parameters
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getProtocolVersion(): string {
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::PROTOCOL_VERSION_1_0)
            ? Http::PROTOCOL_VERSION_1_0 : Http::PROTOCOL_VERSION_1_1;
    }
}