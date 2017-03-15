<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Http\HttpHeadersInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Uri\UriInterface;

interface HttpCargoInterface extends CargoInterface
{
    public const
        OPTION_COOKIES = 'cookies',
        OPTION_ENVIRONMENT = 'environment',
        OPTION_METHOD = 'method',
        OPTION_PARAMETERS = 'parameters',
        OPTION_PROTOCOL_VERSION = 'protocolVersion',
        OPTION_REQUEST_HEADERS = 'requestHeaders',
        OPTION_RESPONSE_HEADERS = 'responseHeaders',
        OPTION_SERVER = 'server',
        OPTION_SESSION = 'session',
        OPTION_STATUS_CODE = 'statusCode',
        OPTION_URI = 'uri';

    public function getCookies(): CookieManagerInterface;
    public function getEnvironment(): ContainerInterface;
    public function getMethod(): string;
    public function getParameters(): VariableContainerInterface;
    public function getProtocolVersion(): string;
    public function getRequestHeaders(): HttpHeadersInterface;
    public function getResponseHeaders(): HttpHeadersInterface;
    public function getServer(): ContainerInterface;
    public function getSession(): SessionManagerInterface;
    public function getStatusCode(): int;
    public function getUri(): UriInterface;
    public function setMethod(string $method): HttpCargoInterface;
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface;
    public function setStatusCode(int $statusCode): HttpCargoInterface;
    public function setUri(UriInterface $requestUri): HttpCargoInterface;
}
