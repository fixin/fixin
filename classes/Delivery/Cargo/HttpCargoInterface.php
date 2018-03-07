<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Header\HeaderManagerInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Uri\UriInterface;

interface HttpCargoInterface extends CargoInterface
{
    public const
        COOKIES = 'cookies',
        ENVIRONMENT = 'environment',
        METHOD = 'method',
        PARAMETERS = 'parameters',
        PROTOCOL_VERSION = 'protocolVersion',
        REQUEST_HEADERS = 'requestHeaders',
        RESPONSE_HEADERS = 'responseHeaders',
        SERVER = 'server',
        SESSION = 'session',
        STATUS_CODE = 'statusCode',
        URI = 'uri';

    /**
     * Get cookies
     *
     * @return CookieManagerInterface
     */
    public function getCookies(): CookieManagerInterface;

    /**
     * Get environment
     *
     * @return ContainerInterface
     */
    public function getEnvironment(): ContainerInterface;

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Get parameters
     *
     * @return VariableContainerInterface
     */
    public function getParameters(): VariableContainerInterface;

    /**
     * Get protocol version
     *
     * @return string
     */
    public function getProtocolVersion(): string;

    /**
     * Get request headers
     *
     * @return HeaderManagerInterface
     */
    public function getRequestHeaders(): HeaderManagerInterface;

    /**
     * Get response headers
     *
     * @return HeaderManagerInterface
     */
    public function getResponseHeaders(): HeaderManagerInterface;

    /**
     * Get server
     *
     * @return ContainerInterface
     */
    public function getServer(): ContainerInterface;

    /**
     * Get session
     *
     * @return SessionManagerInterface
     */
    public function getSession(): SessionManagerInterface;

    /**
     * Get status code
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Get URI
     *
     * @return UriInterface
     */
    public function getUri(): UriInterface;

    /**
     * Set method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): HttpCargoInterface;

    /**
     * Set protocol version
     *
     * @param string $protocolVersion
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface;

    /**
     * Set status code
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface;

    /**
     * Set URI
     *
     * @param UriInterface $requestUri
     * @return $this
     */
    public function setUri(UriInterface $requestUri): HttpCargoInterface;
}
