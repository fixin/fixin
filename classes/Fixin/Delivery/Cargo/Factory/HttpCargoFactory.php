<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Headers\HeadersInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Factory;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Http;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class HttpCargoFactory implements FactoryInterface
{
    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null, string $name = null): HttpCargoInterface
    {
        $cookies = $resourceManager->clone('Base\Cookie\CookieManager', CookieManagerInterface::class, [
            CookieManagerInterface::COOKIES => $_COOKIE
        ]);
        $method = $_SERVER['REQUEST_METHOD'];
        $requestHeaders = $this->getRequestHeaders();

        $options = [
            HttpCargoInterface::COOKIES => $cookies,
            HttpCargoInterface::ENVIRONMENT => $resourceManager->clone('Base\Container\Container', ContainerInterface::class)->withValues($_ENV),
            HttpCargoInterface::METHOD => $method,
            HttpCargoInterface::PARAMETERS => $resourceManager->clone('Base\Container\VariableContainer', VariableContainerInterface::class)->withValues($_GET),
            HttpCargoInterface::PROTOCOL_VERSION => $this->getRequestProtocolVersion(),
            HttpCargoInterface::REQUEST_HEADERS => $resourceManager->clone('Base\Headers\Headers', HeadersInterface::class, [
                HeadersInterface::VALUES => $requestHeaders
            ]),
            HttpCargoInterface::RESPONSE_HEADERS => $resourceManager->clone('Base\Headers\Headers', HeadersInterface::class),
            HttpCargoInterface::SERVER => $resourceManager->clone('Base\Container\Container', ContainerInterface::class)->withValues($_SERVER),
            HttpCargoInterface::SESSION => $resourceManager->clone('Base\Session\SessionManager', SessionManagerInterface::class, [
                SessionManagerInterface::COOKIE_MANAGER => $cookies
            ]),
            HttpCargoInterface::CONTENT_TYPE => 'text/html',
        ];

        if (in_array($method, [Http::POST_METHOD, Http::PUT_METHOD])) {
            $options[HttpCargoInterface::CONTENT] = $this->getPostParameters();

            if (isset($requestHeaders[Http::CONTENT_TYPE_HEADER])) {
                $options[HttpCargoInterface::CONTENT_TYPE] = $requestHeaders[Http::CONTENT_TYPE_HEADER];
            }
        }

        return $resourceManager->clone('Delivery\Cargo\HttpCargo', HttpCargoInterface::class, $options);
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
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::VERSION_1_0)
            ? Http::VERSION_1_0 : Http::VERSION_1_1;
    }
}
