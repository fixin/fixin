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
use Fixin\Base\Header\HeadersInterface;
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

    public function getCookies(): CookieManagerInterface;
    public function getEnvironment(): ContainerInterface;
    public function getMethod(): string;
    public function getParameters(): VariableContainerInterface;
    public function getProtocolVersion(): string;
    public function getRequestHeaders(): HeadersInterface;
    public function getResponseHeaders(): HeadersInterface;
    public function getServer(): ContainerInterface;
    public function getSession(): SessionManagerInterface;
    public function getStatusCode(): int;
    public function getUri(): UriInterface;

    /**
     * @return $this
     */
    public function setMethod(string $method): HttpCargoInterface;

    /**
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface;

    /**
     * @return $this
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface;

    /**
     * @return $this
     */
    public function setUri(UriInterface $requestUri): HttpCargoInterface;
}
