<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Container\VariableContainer;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Factory\Factory;
use Fixin\Support\Http;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class HttpCargoFactory extends Factory
{
    protected const
        DEFAULT_SESSION_COOKIE = 'session';

    public const
        OPTION_SESSION_COOKIE = 'sessionCookie';

    public function __invoke(array $options = NULL, string $name = NULL): HttpCargoInterface
    {
        $container = $this->container;

        $variables = new VariableContainer();
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
     */
    protected function getHeaders(): array
    {
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

    protected function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    protected function getPostParameters(): array
    {
        $post = $_POST;

        // Files
        if ($_FILES) {
            foreach ($_FILES as $key => $file) {
                $post[$key] = $file;
            }
        }

        return $post;
    }

    protected function getProtocolVersion(): string
    {
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::PROTOCOL_VERSION_1_0)
            ? Http::PROTOCOL_VERSION_1_0 : Http::PROTOCOL_VERSION_1_1;
    }

    protected function setup(HttpCargoInterface $cargo)
    {
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
     */
    protected function setupParameters(HttpCargoInterface $cargo)
    {
        $cargo->getRequestParameters()->setFromArray($_GET);
        $cargo->getEnvironmentParameters()->setFromArray($_ENV);
        $cargo->getServerParameters()->setFromArray($_SERVER);
    }

    /**
     * Setup POST data
     */
    protected function setupPost(HttpCargoInterface $cargo)
    {
        $cargo->setContent($this->getPostParameters());

        // Content type
        if ($contentType = $cargo->getRequestHeader(Http::HEADER_CONTENT_TYPE)) {
            $cargo->setContentType($contentType);
        }
    }

    /**
     * Setup request data
     */
    protected function setupRequest(HttpCargoInterface $cargo)
    {
        $cargo
            ->setRequestProtocolVersion($this->getProtocolVersion())
            ->setRequestMethod($this->getMethod())
            ->setRequestUri($this->container->clonePrototype('Base\Uri\Factory\EnvironmentUriFactory'))
            ->setRequestHeaders($this->getHeaders());
    }

    protected function setupSession(CookieManagerInterface $cookies): SessionManagerInterface
    {
        return $this->container->clonePrototype('Base\Session\SessionManager', [
            SessionManagerInterface::OPTION_COOKIE_MANAGER => $cookies
        ]);
    }
}
