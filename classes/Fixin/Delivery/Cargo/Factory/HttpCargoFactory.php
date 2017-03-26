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
use Fixin\Resource\Factory;
use Fixin\Resource\FactoryInterface;
use Fixin\Support\Http;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class HttpCargoFactory extends Factory implements FactoryInterface
{
    public function __invoke(array $options = null, string $name = null): HttpCargoInterface
    {
        $resourceManager = $this->resourceManager;
        $cookies = $resourceManager->clone('Base\Cookie\CookieManager', [
            CookieManagerInterface::COOKIES => $_COOKIE
        ]);
        $method = $_SERVER['REQUEST_METHOD'];
        $requestHeaders = $this->getRequestHeaders();

        $options = [
            HttpCargoInterface::COOKIES => $cookies,
            HttpCargoInterface::ENVIRONMENT => $resourceManager->clone('Base\Container\Container')->withValues($_ENV),
            HttpCargoInterface::METHOD => $method,
            HttpCargoInterface::PARAMETERS => $resourceManager->clone('Base\Container\VariableContainer')->withValues($_GET),
            HttpCargoInterface::PROTOCOL_VERSION => $this->getRequestProtocolVersion(),
            HttpCargoInterface::REQUEST_HEADERS => $resourceManager->clone('Base\Headers\Headers', [
                HeadersInterface::VALUES => $requestHeaders
            ]),
            HttpCargoInterface::RESPONSE_HEADERS => $resourceManager->clone('Base\Headers\Headers'),
            HttpCargoInterface::SERVER => $resourceManager->clone('Base\Container\Container')->withValues($_SERVER),
            HttpCargoInterface::SESSION => $resourceManager->clone('Base\Session\SessionManager', [
                SessionManagerInterface::COOKIE_MANAGER => $cookies
            ]),
            HttpCargoInterface::CONTENT_TYPE => 'text/html',
        ];

        if (in_array($method, [Http::POST_METHOD, Http::PUT_METHOD])) {
            $options[CargoInterface::CONTENT] = $this->getPostParameters();

            if (isset($requestHeaders[Http::CONTENT_TYPE_HEADER])) {
                $options[CargoInterface::CONTENT_TYPE] = $requestHeaders[Http::CONTENT_TYPE_HEADER];
            }
        }

        return $resourceManager->clone('Delivery\Cargo\HttpCargo', $options);
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
