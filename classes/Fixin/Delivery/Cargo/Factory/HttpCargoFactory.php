<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
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
        $cookies = $container->clonePrototype('Base\Cookie\CookieManager', [
            CookieManagerInterface::OPTION_COOKIES => $_COOKIE
        ]);
        $parameters = $container->clonePrototype('Base\Container\Container');
        $requestHeaders = $this->getRequestHeaders();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $options = [
            HttpCargoInterface::OPTION_COOKIES => $cookies,
            HttpCargoInterface::OPTION_ENVIRONMENT_PARAMETERS => $parameters->withOptions([
                ContainerInterface::OPTION_VALUES => $_ENV
            ]),
            HttpCargoInterface::OPTION_REQUEST_HEADERS => $requestHeaders,
            HttpCargoInterface::OPTION_REQUEST_METHOD => $requestMethod,
            HttpCargoInterface::OPTION_REQUEST_PARAMETERS => $parameters->withOptions([
                ContainerInterface::OPTION_VALUES => $_GET
            ]),
            HttpCargoInterface::OPTION_REQUEST_PROTOCOL_VERSION => $this->getRequestProtocolVersion(),
            HttpCargoInterface::OPTION_REQUEST_URI => $container->clonePrototype('Base\Uri\Factory\EnvironmentUriFactory'),
            HttpCargoInterface::OPTION_SERVER_PARAMETERS => $parameters->withOptions([
                ContainerInterface::OPTION_VALUES => $_SERVER
            ]),
            HttpCargoInterface::OPTION_SESSION => $container->clonePrototype('Base\Session\SessionManager', [
                SessionManagerInterface::OPTION_COOKIE_MANAGER => $cookies
            ])
        ];

        if (in_array($requestMethod, [Http::METHOD_POST, Http::METHOD_PUT])) {
            $options[CargoInterface::OPTION_CONTENT] = $this->getPostParameters();

            if (isset($requestHeaders[Http::HEADER_CONTENT_TYPE])) {
                $options[CargoInterface::OPTION_CONTENT_TYPE] = $requestHeaders[Http::HEADER_CONTENT_TYPE];
            }
        }

        return $container->clonePrototype('Delivery\Cargo\HttpCargo', $options);
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
