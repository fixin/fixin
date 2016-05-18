<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Uri\UriInterface;
use Fixin\Support\{Http, ToStringTrait};

class HttpCargo extends Cargo implements HttpCargoInterface {

    use ToStringTrait;

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @var array
     */
    protected $environmentParameters = [];

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
     * @var array
     */
    protected $requestParameters = [];

    /**
     * @var string
     */
    protected $requestProtocolVersion = Http::PROTOCOL_VERSION_1_1;

    /**
     * @var UriInterface
     */
    protected $requestUri;

    /**
     * @var array
     */
    protected $serverParameters = [];

    /**
     * @var int
     */
    protected $statusCode = Http::STATUS_OK_200;

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::addHeader($name, $value)
     */
    public function addHeader(string $name, string $value) {
        $this->headers[$name][] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::clearHeaders()
     */
    public function clearHeaders() {
        $this->headers = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\Cargo::getContentType()
     */
    public function getContentType() {
        return $this->headers[Http::HEADER_CONTENT_TYPE][0] ?? [];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getCookie($name)
     */
    public function getCookie(string $name) {
        return $this->cookies[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getEnvironmentParameter($name, $default)
     */
    public function getEnvironmentParameter(string $name, string $default = null) {
        return $this->environmentParameters[$name] ?? $default;
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
    public function getRequestHeader(string $name): string {
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
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getRequestParameter($name, $default)
     */
    public function getRequestParameter(string $name, string $default = null) {
        return $this->requestParameters[$name] ?? $default;
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
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getServerParameter($name, $default)
     */
    public function getServerParameter(string $name, string $default = null) {
        return $this->serverParameters[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::getStatusCode()
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setCookies($cookies)
     */
    public function setCookies(array $cookies) {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\Cargo::setContentType()
     */
    public function setContentType($contentType) {
        $this->headers[Http::HEADER_CONTENT_TYPE] = [$contentType];

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setEnvironmentParameters($parameters)
     */
    public function setEnvironmentParameters(array $parameters) {
        $this->environmentParameters = $parameters;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setHeader($name, $value)
     */
    public function setHeader(string $name, $value) {
        $this->headers[$name] = is_scalar($value) ? [$value] : $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setProtocolVersion($protocolVersion)
     */
    public function setProtocolVersion(string $protocolVersion) {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestHeaders($headers)
     */
    public function setRequestHeaders(array $headers) {
        $this->requestHeaders = $headers;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestMethod($method)
     */
    public function setRequestMethod(string $method) {
        $this->requestMethod = $method;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestParameters($parameters)
     */
    public function setRequestParameters(array $parameters) {
        $this->requestParameters = $parameters;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestProtocolVersion($protocolVersion)
     */
    public function setRequestProtocolVersion(string $protocolVersion) {
        $this->requestProtocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setRequestUri($requestUri)
     */
    public function setRequestUri(UriInterface $requestUri) {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setServerParameters($parameters)
     */
    public function setServerParameters(array $parameters) {
        $this->serverParameters = $parameters;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\HttpCargoInterface::setStatusCode($statusCode)
     */
    public function setStatusCode(int $statusCode) {
        $this->statusCode = $statusCode;

        return $this;
    }
}