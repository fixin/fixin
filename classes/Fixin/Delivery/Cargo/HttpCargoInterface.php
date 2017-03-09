<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Uri\UriInterface;

interface HttpCargoInterface extends CargoInterface
{
    public const
        OPTION_COOKIES = 'cookies',
        OPTION_ENVIRONMENT_PARAMETERS = 'environmentParameters',
        OPTION_PROTOCOL_VERSION = 'protocolVersion',
        OPTION_REQUEST_HEADERS = 'requestHeaders',
        OPTION_REQUEST_METHOD = 'requestMethod',
        OPTION_REQUEST_PARAMETERS = 'requestParameters',
        OPTION_REQUEST_PROTOCOL_VERSION = 'requestProtocolVersion',
        OPTION_REQUEST_URI = 'requestUri',
        OPTION_SERVER_PARAMETERS = 'serverParameters',
        OPTION_SESSION = 'session',
        OPTION_STATUS_CODE = 'statusCode';

    /**
     * Add header value
     */
    public function addHeader(string $name, string $value): HttpCargoInterface;

    /**
     * Clear all headers
     */
    public function clearHeaders(): HttpCargoInterface;

    public function getCookies(): CookieManagerInterface;
    public function getEnvironmentParameters(): ContainerInterface;

    /**
     * Get header values
     */
    public function getHeaders(): array;

    public function getProtocolVersion(): string;

    /**
     * Get request header value
     */
    public function getRequestHeader(string $name): ?string;

    public function getRequestMethod(): string;
    public function getRequestParameters(): ContainerInterface;
    public function getRequestProtocolVersion(): string;
    public function getRequestUri(): UriInterface;
    public function getServerParameters(): ContainerInterface;
    public function getSession(): SessionManagerInterface;
    public function getStatusCode(): int;

    /**
     * Set header value
     *
     * @param string|array $value
     */
    public function setHeader(string $name, $value): HttpCargoInterface;
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface;
    public function setStatusCode(int $statusCode): HttpCargoInterface;
}
