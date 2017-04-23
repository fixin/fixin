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
use Fixin\Support\DebugDescriptionTrait;
use Fixin\Support\Http;
use Fixin\Support\Types;

class HttpCargo extends CargoBase implements HttpCargoInterface
{
    use DebugDescriptionTrait;

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::CONTENT_TYPE => [self::USING_SETTER, Types::NULL],
            self::COOKIES => CookieManagerInterface::class,
            self::ENVIRONMENT => ContainerInterface::class,
            self::METHOD => self::USING_SETTER,
            self::PARAMETERS => VariableContainerInterface::class,
            self::PROTOCOL_VERSION => self::USING_SETTER,
            self::REQUEST_HEADERS => HeadersInterface::class,
            self::RESPONSE_HEADERS => HeadersInterface::class,
            self::SERVER => ContainerInterface::class,
            self::SESSION => SessionManagerInterface::class,
            self::STATUS_CODE => self::USING_SETTER,
            self::URI => self::USING_SETTER
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
    protected $method = Http::GET_METHOD;

    /**
     * @var VariableContainerInterface
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $protocolVersion = Http::VERSION_1_1;

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
        return $this->getResponseHeaders()->get(Http::CONTENT_TYPE_HEADER)[0] ?? '';
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
     * @return $this
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->responseHeaders->set(Http::CONTENT_TYPE_HEADER, [$contentType]);

        return $this;
    }

    /**
     * @return $this
     */
    public function setMethod(string $method): HttpCargoInterface
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * @return $this
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return $this
     */
    public function setUri(UriInterface $uri): HttpCargoInterface
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return $this
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
