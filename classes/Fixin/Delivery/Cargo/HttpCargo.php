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
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Uri\UriInterface;
use Fixin\Support\Http;
use Fixin\Support\ToStringTrait;

class HttpCargo extends Cargo implements HttpCargoInterface
{
    use ToStringTrait;

    protected const THIS_REQUIRES = [
            self::OPTION_COOKIES => self::TYPE_INSTANCE,
            self::OPTION_ENVIRONMENT_PARAMETERS => self::TYPE_INSTANCE,
            self::OPTION_REQUEST_PARAMETERS => self::TYPE_INSTANCE,
            self::OPTION_SERVER_PARAMETERS => self::TYPE_INSTANCE,
            self::OPTION_SESSION => self::TYPE_INSTANCE
        ];

    /**
     * @var CookieManagerInterface
     */
    protected $cookies;

    /**
     * @var ContainerInterface
     */
    protected $environmentParameters;

    /**
     * @var array
     */
    protected $headers = [Http::HEADER_CONTENT_TYPE => ['text/html']];

    /**
     * @var string
     */
    protected $protocolVersion = Http::PROTOCOL_VERSION_1_1;

    /**
     * @var array
     */
    protected $requestHeaders = [];

    /**
     * @var string
     */
    protected $requestMethod = Http::METHOD_GET;

    /**
     * @var VariableContainerInterface
     */
    protected $requestParameters;

    /**
     * @var string
     */
    protected $requestProtocolVersion = Http::PROTOCOL_VERSION_1_1;

    /**
     * @var UriInterface
     */
    protected $requestUri;

    /**
     * @var ContainerInterface
     */
    protected $serverParameters;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var int
     */
    protected $statusCode = Http::STATUS_CONTINUE_100;

    /**
     * @return static
     */
    public function addHeader(string $name, string $value): HttpCargoInterface
    {
        $this->headers[$name][] = $value;

        return $this;
    }

    /**
     * @return static
     */
    public function clearHeaders(): HttpCargoInterface
    {
        $this->headers = [];

        return $this;
    }

    public function getContentType(): string
    {
        return $this->headers[Http::HEADER_CONTENT_TYPE][0] ?? '';
    }

    public function getCookies(): CookieManagerInterface
    {
        return $this->cookies;
    }

    public function getEnvironmentParameters(): ContainerInterface
    {
        return $this->environmentParameters;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getRequestHeader(string $name): ?string
    {
        return $this->requestHeaders[$name] ?? null;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getRequestParameters(): VariableContainerInterface
    {
        return $this->requestParameters;
    }

    public function getRequestProtocolVersion(): string
    {
        return $this->requestProtocolVersion;
    }

    public function getRequestUri(): UriInterface
    {
        return $this->requestUri;
    }

    public function getServerParameters(): ContainerInterface
    {
        return $this->serverParameters;
    }

    public function getSession(): SessionManagerInterface
    {
        return $this->session;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return static
     */
    protected function sendHeaders(): self
    {
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                header("$name: " . $value, false);
            }
        }

        return $this;
    }

    /**
     * @return static
     */
    protected function sendStatusCode(): self
    {
        http_response_code($this->statusCode);

        return $this;
    }

    /**
     * @return static
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->headers[Http::HEADER_CONTENT_TYPE] = [$contentType];

        return $this;
    }

    protected function setCookies(CookieManagerInterface $cookies): void
    {
        $this->cookies = $cookies;
    }

    protected function setEnvironmentParameters(ContainerInterface $parameters): void
    {
        $this->environmentParameters = $parameters;
    }

    /**
     * @return static
     */
    public function setHeader(string $name, $value): HttpCargoInterface
    {
        $this->headers[$name] = is_scalar($value) ? [$value] : $value;

        return $this;
    }

    /**
     * @return static
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * @return static
     */
    public function setRequestHeaders(array $headers): HttpCargoInterface
    {
        $this->requestHeaders = $headers;

        return $this;
    }

    /**
     * @return static
     */
    public function setRequestMethod(string $method): HttpCargoInterface
    {
        $this->requestMethod = $method;

        return $this;
    }

    protected function setRequestParameters(ContainerInterface $parameters): void
    {
        $this->requestParameters = $parameters;
    }

    /**
     * @return static
     */
    public function setRequestProtocolVersion(string $protocolVersion): HttpCargoInterface
    {
        $this->requestProtocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * @return static
     */
    public function setRequestUri(UriInterface $requestUri): HttpCargoInterface
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    protected function setServerParameters(ContainerInterface $parameters): void
    {
        $this->serverParameters = $parameters;
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
    public function unpack(): CargoInterface
    {
        // Cookie changes
        $this->cookies->sendChanges();

        // Headers
        $this
            ->sendHeaders()
            ->sendStatusCode();

        // Parent
        parent::unpack();

        // Session
        if ($this->session) {
            $this->session->save();
        }

        return $this;
    }
}
