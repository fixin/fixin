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
    protected $environment = [];

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
     * @var string
     */
    protected $requestUri;

    /**
     * @var array
     */
    protected $server = [];

    /**
     * @var int
     */
    protected $statusCode = Http::STATUS_OK_200;

    /**
     * Gets environment parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param mixed $default
     * @return string|mixed
     */
    public function getEnvironment(string $name, $default = null) {
        return $this->environment[$name] ?? $default;
    }

    /**
     * Gets server parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param mixed $default
     * @return string|mixed
     */
    public function getServer(string $name, $default = null) {
        return $this->server[$name] ?? $default;
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
     * Sets environment values
     *
     * @param array $environment
     * @return self
     */
    public function setEnvironment(array $environment) {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Sets server values
     *
     * @param array $server
     * @return self
     */
    public function setServer(array $server) {
        $this->server = $server;

        return $this;
    }

    /* input
     * contentType (charset), contentLength,
     * body / post + uploadedFiles
     */

    /* output
     * contentType (charset), contentLength
     */

    public function __toString() {
        return '<pre>' . htmlspecialchars(print_r($this, true)) . '</pre>';
    }
}