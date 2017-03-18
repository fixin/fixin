<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Headers\HeadersInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Factory\Factory;
use Fixin\Support\Http;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class HttpCargoFactory extends Factory
{
    public function __invoke(array $options = null, string $name = null): HttpCargoInterface
    {
        $container = $this->container;
        $cookies = $container->clone('Base\Cookie\CookieManager', [
            CookieManagerInterface::OPTION_COOKIES => $_COOKIE
        ]);
        $method = $_SERVER['REQUEST_METHOD'];
        $requestHeaders = $this->getRequestHeaders();

        // TODO
        var_dump($requestHeaders);
        die;

        $options = [
            HttpCargoInterface::OPTION_COOKIES => $cookies,
            HttpCargoInterface::OPTION_ENVIRONMENT => $container->clone('Base\Container\Container')->withValues($_ENV),
            HttpCargoInterface::OPTION_METHOD => $method,
            HttpCargoInterface::OPTION_PARAMETERS => $container->clone('Base\Container\VariableContainer')->withValues($_GET),
            HttpCargoInterface::OPTION_PROTOCOL_VERSION => $this->getRequestProtocolVersion(),
            HttpCargoInterface::OPTION_REQUEST_HEADERS => $container->clone('Base\Headers\Headers', [
                HeadersInterface::OPTION_VALUES => $requestHeaders
            ]),
            HttpCargoInterface::OPTION_RESPONSE_HEADERS => $container->clone('Base\Headers\Headers'),
            HttpCargoInterface::OPTION_SERVER => $container->clone('Base\Container\Container')->withValues($_SERVER),
            HttpCargoInterface::OPTION_SESSION => $container->clone('Base\Session\SessionManager', [
                SessionManagerInterface::OPTION_COOKIE_MANAGER => $cookies
            ]),
            HttpCargoInterface::OPTION_CONTENT_TYPE => 'text/html',
        ];

        if (in_array($method, [Http::METHOD_POST, Http::METHOD_PUT])) {
            $options[CargoInterface::OPTION_CONTENT] = $this->getPostParameters();

            if (isset($requestHeaders[Http::HEADER_CONTENT_TYPE])) {
                $options[CargoInterface::OPTION_CONTENT_TYPE] = $requestHeaders[Http::HEADER_CONTENT_TYPE];
            }
        }

        return $container->clone('Delivery\Cargo\HttpCargo', $options);
    }

    protected function getPostParameters(): array
    {
        $post = $_POST;

        // Files
        if ($_FILES) {
            foreach ($_FILES as $key => $file) {
                $post[$key] = $file; // TODO pre-process
            }
        }

        return $post;
    }

    protected function getRequestHeaders(): array
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

    protected function getRequestProtocolVersion(): string
    {
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::PROTOCOL_VERSION_1_0)
            ? Http::PROTOCOL_VERSION_1_0 : Http::PROTOCOL_VERSION_1_1;
    }
}
