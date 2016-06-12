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
use Fixin\Resource\Resource;
use Fixin\Support\Http;
use Fixin\Support\ToStringTrait;

class HttpCargo extends Cargo implements HttpCargoInterface {

    use ToStringTrait;

    /**
     * @var CookieManagerInterface
     */
    protected $cookies;

    /**
     * @var VariableContainerInterface
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
     * @var VariableContainerInterface
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
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::addHeader($name, $value)
     */
    public function addHeader(string $name, string $value): HttpCargoInterface {
        $this->headers[$name][] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::clearHeaders()
     */
    public function clearHeaders(): HttpCargoInterface {
        $this->headers = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (!isset($this->cookies)) {
            throw new RuntimeException(static::EXCEPTION_COOKIES_NOT_SET);
        }

        if (!isset($this->session)) {
            throw new RuntimeException(static::EXCEPTION_SESSION_NOT_SET);
        }

        $this->configurationTestsParameters();

        return $this;
    }

    /**
     * Parameters
     *
     * @throws RuntimeException
     */
    protected function configurationTestsParameters() {
        if (!isset($this->environmentParameters)) {
            throw new RuntimeException(static::EXCEPTION_ENVIRONMENT_PARAMETERS_NOT_SET);
        }

        if (!isset($this->requestParameters)) {
            throw new RuntimeException(static::EXCEPTION_REQUEST_PARAMETERS_NOT_SET);
        }

        if (!isset($this->serverParameters)) {
            throw new RuntimeException(static::EXCEPTION_SERVER_PARAMETERS_NOT_SET);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\Cargo::getContentType()
     */
    public function getContentType(): string {
        return $this->headers[Http::HEADER_CONTENT_TYPE][0] ?? '';
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getCookies()
     */
    public function getCookies(): CookieManagerInterface {
        return $this->cookies;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getEnvironmentParameters()
     */
    public function getEnvironmentParameters(): VariableContainerInterface {
        return $this->environmentParameters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getHeaders()
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getProtocolVersion()
     */
    public function getProtocolVersion(): string {
        return $this->protocolVersion;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getRequestHeader($name)
     */
    public function getRequestHeader(string $name) {
        return $this->requestHeaders[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getRequestMethod()
     */
    public function getRequestMethod(): string {
        return $this->requestMethod;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getRequestParameters()
     */
    public function getRequestParameters(): VariableContainerInterface {
        return $this->requestParameters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getRequestProtocolVersion()
     */
    public function getRequestProtocolVersion(): string {
        return $this->requestProtocolVersion;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getRequestUri()
     */
    public function getRequestUri() {
        return $this->requestUri;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getServerParameters()
     */
    public function getServerParameters(): VariableContainerInterface {
        return $this->serverParameters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getSession()
     */
    public function getSession(): SessionManagerInterface {
        return $this->session;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getStatusCode()
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setContentType($contentType)
     */
    public function setContentType(string $contentType): CargoInterface {
        $this->headers[Http::HEADER_CONTENT_TYPE] = [$contentType];

        return $this;
    }

    /**
     * Set cookie manager
     *
     * @param CookieManagerInterface $cookies
     */
    protected function setCookies(CookieManagerInterface $cookies) {
        $this->cookies = $cookies;
    }

    /**
     * Set enviroment parameter container
     *
     * @param VariableContainerInterface $parameters
     */
    protected function setEnvironmentParameters(VariableContainerInterface $parameters) {
        $this->environmentParameters = $parameters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setHeader($name, $value)
     */
    public function setHeader(string $name, $value): HttpCargoInterface {
        $this->headers[$name] = is_scalar($value) ? [$value] : $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setProtocolVersion($protocolVersion)
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * Set request parameter container
     *
     * @param VariableContainerInterface $parameters
     */
    protected function setRequestParameters(VariableContainerInterface $parameters) {
        $this->requestParameters = $parameters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestHeaders($headers)
     */
    public function setRequestHeaders(array $headers): HttpCargoInterface {
        $this->requestHeaders = $headers;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestMethod($method)
     */
    public function setRequestMethod(string $method): HttpCargoInterface {
        $this->requestMethod = $method;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestProtocolVersion($protocolVersion)
     */
    public function setRequestProtocolVersion(string $protocolVersion): HttpCargoInterface {
        $this->requestProtocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestUri($requestUri)
     */
    public function setRequestUri(UriInterface $requestUri): HttpCargoInterface {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * Set server parameter container
     *
     * @param VariableContainerInterface $parameters
     */
    protected function setServerParameters(VariableContainerInterface $parameters) {
        $this->serverParameters = $parameters;
    }

    /**
     * Set session
     *
     * @param SessionManagerInterface $session
     */
    protected function setSession(SessionManagerInterface $session) {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setStatusCode($statusCode)
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\Cargo::unpack()
     */
    public function unpack() {
        // Cookie changes
        $this->cookies->sendChanges();

        // Content
        echo $this->content;
    }
}