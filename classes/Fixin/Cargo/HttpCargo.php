<?php

namespace Fixin\Cargo;

use Fixin\Support\Http;

class HttpCargo extends Cargo {

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
    protected $headers = [];

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
     * @var \stdClass
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
     * Adds header value
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function addHeader(string $name, string $value) {
        $this->headers[$name][] = $value;

        return $this;
    }

    /**
     * Clears all headers
     *
     * @return self
     */
    public function clearHeaders() {
        $this->headers = [];

        return $this;
    }

    /**
     * Gets cookie value
     *
     * @param string $name
     * @return string|null
     */
    public function getCookie(string $name) {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Gets environment parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param string $default
     * @return string|null
     */
    public function getEnvironmentParameter(string $name, string $default = null) {
        return $this->environmentParameters[$name] ?? $default;
    }

    /**
     * Gets header values
     *
     * @return array
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * Gets protocol version
     *
     * @return string
     */
    public function getProtocolVersion(): string {
        return $this->protocolVersion;
    }

    /**
     * Gets request header value
     *
     * @param string $name
     * @return string|null
     */
    public function getRequestHeader(string $name) {
        return $this->requestHeaders[$name] ?? null;
    }

    /**
     * Gets request method
     *
     * @return string
     */
    public function getRequestMethod(): string {
        return $this->requestMethod;
    }

    /**
     * Gets server parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param string $default
     * @return string|null
     */
    public function getRequestParameter(string $name, string $default = null) {
        return $this->requestParameters[$name] ?? $default;
    }

    /**
     * Gets request protocol version
     *
     * @return string
     */
    public function getRequestProtocolVersion(): string {
        return $this->requestProtocolVersion;
    }

    /**
     * Gets request URI
     *
     * @return string
     */
    public function getRequestUri() {
        return $this->requestUri;
    }

    /**
     * Gets server parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getServerParameter(string $name, string $default = null) {
        return $this->serverParameters[$name] ?? $default;
    }

    /**
     * Gets status code
     *
     * @return number
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Sets cookies
     *
     * @param array $cookies
     * @return self
     */
    public function setCookies(array $cookies) {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * Sets environment parameters
     *
     * @param array $parameters
     * @return self
     */
    public function setEnvironmentParameters(array $parameters) {
        $this->environment = $parameters;

        return $this;
    }

    /**
     * Sets protocol version
     *
     * @param string $protocolVersion
     * @return self
     */
    public function setProtocolVersion(string $protocolVersion) {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * Sets request header values
     *
     * @param array $headers
     * @return self
     */
    public function setRequestHeaders(array $headers) {
        $this->requestHeaders = $headers;

        return $this;
    }
    /**
     * Sets request method
     *
     * @param string $method
     * @return self
     */
    public function setRequestMethod(string $method) {
        $this->requestMethod = $method;
        return $this;
    }

    /**
     * Sets request parameters
     *
     * @param array $parameters
     * @return self
     */
    public function setRequestParameters(array $parameters) {
        $this->requestParameters = $parameters;

        return $this;
    }

    /**
     * Sets request protocol version
     *
     * @param string $protocolVersion
     * @return self
     */
    public function setRequestProtocolVersion(string $protocolVersion) {
        $this->requestProtocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * Sets request URI
     *
     * @param \stdClass $requestUri
     * @return self
     */
    public function setRequestUri(\stdClass $requestUri) {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * Sets server parameters
     *
     * @param array $parameters
     * @return self
     */
    public function setServerParameters(array $parameters) {
        $this->serverParameters = $parameters;

        return $this;
    }

    /**
     * Sets status code
     *
     * @param int $statusCode
     * @return self
     */
    public function setStatusCode(int $statusCode) {
        $this->statusCode = $statusCode;

        return $this;
    }
}