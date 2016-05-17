<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Uri\UriInterface;

interface HttpCargoInterface extends CargoInterface {

    /**
     * Add header value
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function addHeader(string $name, string $value);

    /**
     * Clear all headers
     *
     * @return self
     */
    public function clearHeaders();

    /**
     * Get cookie value
     *
     * @param string $name
     * @return string|null
     */
    public function getCookie(string $name);

    /**
     * Get environment parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param string $default
     * @return string|null
     */
    public function getEnvironmentParameter(string $name, string $default = null);

    /**
     * Get header values
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Get protocol version
     *
     * @return string
     */
    public function getProtocolVersion(): string;

    /**
     * Get request header value
     *
     * @param string $name
     * @return string|null
     */
    public function getRequestHeader(string $name);

    /**
     * Get request method
     *
     * @return string
     */
    public function getRequestMethod(): string;

    /**
     * Get server parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param string $default
     * @return mixed|null
     */
    public function getRequestParameter(string $name, string $default = null);

    /**
     * Get request protocol version
     *
     * @return string
     */
    public function getRequestProtocolVersion(): string;

    /**
     * Get request URI
     *
     * @return UriInterface
     */
    public function getRequestUri();

    /**
     * Get server parameter or returns default value when the parameter is missing
     *
     * @param string $name
     * @param string $default
     * @return string|null
     */
    public function getServerParameter(string $name, string $default = null);

    /**
     * Get status code
     *
     * @return number
     */
    public function getStatusCode();

    /**
     * Set cookies
     *
     * @param array $cookies
     * @return self
     */
    public function setCookies(array $cookies);

    /**
     * Set environment parameters
     *
     * @param array $parameters
     * @return self
     */
    public function setEnvironmentParameters(array $parameters);

    /**
     * Set header value
     *
     * @param string $name
     * @param string|array $value
     * @return self
     */
    public function setHeader(string $name, $value);

    /**
     * Set protocol version
     *
     * @param string $protocolVersion
     * @return self
     */
    public function setProtocolVersion(string $protocolVersion);

    /**
     * Set request header values
     *
     * @param array $headers
     * @return self
     */
    public function setRequestHeaders(array $headers);

    /**
     * Set request method
     *
     * @param string $method
     * @return self
     */
    public function setRequestMethod(string $method);

    /**
     * Set request parameters
     *
     * @param array $parameters
     * @return self
     */
    public function setRequestParameters(array $parameters);

    /**
     * Set request protocol version
     *
     * @param string $protocolVersion
     * @return self
     */
    public function setRequestProtocolVersion(string $protocolVersion);

    /**
     * Set request URI
     *
     * @param UriInterface $requestUri
     * @return self
     */
    public function setRequestUri(UriInterface $requestUri);

    /**
     * Set server parameters
     *
     * @param array $parameters
     * @return self
     */
    public function setServerParameters(array $parameters);

    /**
     * Set status code
     *
     * @param int $statusCode
     * @return self
     */
    public function setStatusCode(int $statusCode);
}