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
use Fixin\Base\Headers\HeadersInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Uri\UriInterface;
use Fixin\Support\Http;
use Fixin\Support\ToStringTrait;

class HttpCargo extends Cargo implements HttpCargoInterface
{
    use ToStringTrait;

    protected const THIS_REQUIRES = [
        self::OPTION_COOKIES => self::TYPE_INSTANCE,
        self::OPTION_ENVIRONMENT => self::TYPE_INSTANCE,
        self::OPTION_PARAMETERS => self::TYPE_INSTANCE,
        self::OPTION_REQUEST_HEADERS => self::TYPE_INSTANCE,
        self::OPTION_RESPONSE_HEADERS => self::TYPE_INSTANCE,
        self::OPTION_SERVER => self::TYPE_INSTANCE,
        self::OPTION_SESSION => self::TYPE_INSTANCE,
        self::OPTION_URI => self::TYPE_INSTANCE
    ];

    /**
     * @var CookieManagerInterface
     */
    protected $cookies;

    /**
     * @var ContainerInterface
     */
    protected $environment;

    /**
     * @var string
     */
    protected $method = Http::METHOD_GET;

    /**
     * @var VariableContainerInterface
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $protocolVersion = Http::PROTOCOL_VERSION_1_1;

    /**
     * @var HeadersInterface
     */
    protected $requestHeaders;

    /**
     * @var HeadersInterface
     */
    protected $responseHeaders;

    /**
     * @var ContainerInterface
     */
    protected $server;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var int
     */
    protected $statusCode = Http::STATUS_CONTINUE_100;

    /**
     * @var UriInterface
     */
    protected $uri;

    public function getContentType(): string
    {
        return $this->getResponseHeaders()->get(Http::HEADER_CONTENT_TYPE)[0] ?? '';
    }

    public function getCookies(): CookieManagerInterface
    {
        return $this->cookies;
    }

    public function getEnvironment(): ContainerInterface
    {
        return $this->environment;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParameters(): VariableContainerInterface
    {
        return $this->parameters;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getRequestHeaders(): HeadersInterface
    {
        return $this->requestHeaders;
    }

    public function getResponseHeaders(): HeadersInterface
    {
        return $this->responseHeaders;
    }

    public function getServer(): ContainerInterface
    {
        return $this->server;
    }

    public function getSession(): SessionManagerInterface
    {
        return $this->session;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    protected function sendStatusCode(): void
    {
        http_response_code($this->statusCode);
    }

    /**
     * @return static
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->responseHeaders->set(Http::HEADER_CONTENT_TYPE, [$contentType]);

        return $this;
    }

    protected function setCookies(CookieManagerInterface $cookies): void
    {
        $this->cookies = $cookies;
    }

    protected function setEnvironment(ContainerInterface $parameters): void
    {
        $this->environment = $parameters;
    }

    /**
     * @return static
     */
    public function setMethod(string $method): HttpCargoInterface
    {
        $this->method = $method;

        return $this;
    }

    protected function setParameters(VariableContainerInterface $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return static
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    protected function setRequestHeaders(HeadersInterface $requestHeaders): void
    {
        $this->requestHeaders = $requestHeaders;
    }

    protected function setResponseHeaders(HeadersInterface $responseHeaders): void
    {
        $this->responseHeaders = $responseHeaders;
    }

    protected function setServer(ContainerInterface $server): void
    {
        $this->server = $server;
    }

    protected function setSession(SessionManagerInterface $session): void
    {
        $this->session = $session;
    }

    /**
     * @return static
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return static
     */
    public function setUri(UriInterface $uri): HttpCargoInterface
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return static
     */
    public function unpack(): CargoInterface
    {
        // Cookie changes
        $this->cookies->sendChanges();

        // Header
        $this->responseHeaders->send();
        $this->sendStatusCode();

        // Parent
        parent::unpack();

        // Session
        if ($this->session) {
            $this->session->save();
        }

        return $this;
    }
}
