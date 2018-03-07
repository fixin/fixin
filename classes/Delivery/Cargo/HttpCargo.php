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
use Fixin\Support\DebugDescriptionTrait;
use Fixin\Support\Http;
use Fixin\Support\Types;

class HttpCargo extends AbstractCargo implements HttpCargoInterface
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
            self::REQUEST_HEADERS => HeaderManagerInterface::class,
            self::RESPONSE_HEADERS => HeaderManagerInterface::class,
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
     * @var HeaderManagerInterface
     */
    protected $requestHeaders;

    /**
     * @var HeaderManagerInterface
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

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return $this->getResponseHeaders()->get(Http::CONTENT_TYPE_HEADER)[0] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getCookies(): CookieManagerInterface
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function getEnvironment(): ContainerInterface
    {
        return $this->environment;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): VariableContainerInterface
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function getRequestHeaders(): HeaderManagerInterface
    {
        return $this->requestHeaders;
    }

    /**
     * @inheritDoc
     */
    public function getResponseHeaders(): HeaderManagerInterface
    {
        return $this->responseHeaders;
    }

    /**
     * @inheritDoc
     */
    public function getServer(): ContainerInterface
    {
        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function getSession(): SessionManagerInterface
    {
        return $this->session;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Send status code
     */
    protected function sendStatusCode(): void
    {
        http_response_code($this->statusCode);
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->responseHeaders->set(Http::CONTENT_TYPE_HEADER, [$contentType]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMethod(string $method): HttpCargoInterface
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUri(UriInterface $uri): HttpCargoInterface
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @inheritDoc
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
