<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Uri\UriInterface;

interface HttpCargoInterface extends CargoInterface
{
    public const
        OPTION_COOKIES = 'cookies',
        OPTION_ENVIRONMENT_PARAMETERS = 'environmentParameters',
        OPTION_REQUEST_PARAMETERS = 'requestParameters',
        OPTION_SERVER_PARAMETERS = 'serverParameters',
        OPTION_SESSION = 'session';

    /**
     * Add header value
     */
    public function addHeader(string $name, string $value): HttpCargoInterface;

    /**
     * Clear all headers
     */
    public function clearHeaders(): HttpCargoInterface;

    public function getCookies(): CookieManagerInterface;
    public function getEnvironmentParameters(): VariableContainerInterface;

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
    public function getRequestParameters(): VariableContainerInterface;
    public function getRequestProtocolVersion(): string;
    public function getRequestUri(): UriInterface;
    public function getServerParameters(): VariableContainerInterface;
    public function getSession(): SessionManagerInterface;
    public function getStatusCode(): int;

    /**
     * Set header value
     *
     * @param string|array $value
     */
    public function setHeader(string $name, $value): HttpCargoInterface;

    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface;

    /**
     * Set request header values
     */
    public function setRequestHeaders(array $headers): HttpCargoInterface;

    public function setRequestMethod(string $method): HttpCargoInterface;
    public function setRequestProtocolVersion(string $protocolVersion): HttpCargoInterface;
    public function setRequestUri(UriInterface $requestUri): HttpCargoInterface;
    public function setStatusCode(int $statusCode): HttpCargoInterface;
}
