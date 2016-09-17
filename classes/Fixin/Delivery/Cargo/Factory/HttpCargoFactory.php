<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Factory\Factory;
use Fixin\Support\Http;

class HttpCargoFactory extends Factory {

    const DEFAULT_SESSION_COOKIE = 'session';
    const OPTION_SESSION_COOKIE = 'sessionCookie';

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Factory\FactoryInterface::__invoke()
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(array $options = NULL, string $name = NULL) {
        $container = $this->container;

        $variables = $container->clonePrototype('Base\Container\VariableContainer');
        $cookies = $container->clonePrototype('Base\Cookie\CookieManager', [
            CookieManagerInterface::OPTION_COOKIES => $_COOKIE
        ]);

        /** @var HttpCargoInterface $cargo */
        $cargo = $container->clonePrototype('Delivery\Cargo\HttpCargo', [
            'environmentParameters' => $variables,
            'requestParameters' => clone $variables,
            'serverParameters' => clone $variables,
            'session' => $this->setupSession($cookies),
            'cookies' => $cookies
        ]);

        $this->setup($cargo);

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

    /**
     * Setup cargo
     *
     * @param HttpCargoInterface $cargo
     */
    protected function setup(HttpCargoInterface $cargo) {
        // Setup data
        $this->setupRequest($cargo);
        $this->setupParameters($cargo);

        // POST
        if ($cargo->getRequestMethod() === Http::METHOD_POST) {
            $this->setupPost($cargo);
        }
    }

    /**
     * Setup parameter containers
     *
     * @param HttpCargoInterface $cargo
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function setupParameters(HttpCargoInterface $cargo) {
        $cargo->getRequestParameters()->setFromArray($_GET);
        $cargo->getEnvironmentParameters()->setFromArray($_ENV);
        $cargo->getServerParameters()->setFromArray($_SERVER);
    }

    /**
     * Setup POST data
     *
     * @param HttpCargoInterface $cargo
     */
    protected function setupPost(HttpCargoInterface $cargo) {
        $cargo->setContent($this->getPostParameters());

        // Content type
        if ($contentType = $cargo->getRequestHeader(Http::HEADER_CONTENT_TYPE)) {
            $cargo->setContentType($contentType);
        }
    }

    /**
     * Setup request data
     *
     * @param HttpCargoInterface $cargo
     */
    protected function setupRequest(HttpCargoInterface $cargo) {
        $cargo
        ->setRequestProtocolVersion($this->getProtocolVersion())
        ->setRequestMethod($this->getMethod())
        ->setRequestUri($this->container->clonePrototype('Base\Uri\Factory\EnvironmentUriFactory'))
        ->setRequestHeaders($this->getHeaders());
    }

    /**
     * Setup session
     *
     * @param CookieManagerInterface $cookies
     * @return SessionManagerInterface
     */
    protected function setupSession(CookieManagerInterface $cookies): SessionManagerInterface {
        // TODO: locking

        return $this->container->clonePrototype('Base\Session\SessionManager', [
            SessionManagerInterface::OPTION_COOKIE_MANAGER => $cookies
        ]);
    }
}