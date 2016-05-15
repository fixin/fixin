<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri;

use Fixin\Support\PrototypeInterface;

class Uri implements UriInterface, PrototypeInterface {

    /**
     * @var array
     */
    protected $defaultSchemePorts = [
        'http' => 80,
        'https' => 443,
    ];

    /**
     * @var string|null
     */
    protected $fragment;

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var string
     */
    protected $scheme = '';

    /**
     * @var string
     */
    protected $userInfo = '';

    /**
     * @return string
     */
    public function __toString() {
        return ltrim($this->scheme . '://', ':/')
            . $this->getAuthority()
            . ($this->path !== '' ? '/' . ltrim($this->path, '/') : '')
            . rtrim('?' . $this->query, '?')
            . rtrim('#' . $this->fragment, '#');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getAuthority()
     */
    public function getAuthority(): string {
        // Host
        $authority = $this->host;

        if ($authority === '') {
            return '';
        }

        // User
        if ($this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        // Port
        if (!$this->isStandardPort($this->port, $this->scheme)) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getFragment()
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getHost()
     */
    public function getHost(): string {
        return $this->host;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getPath()
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getPort()
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getQuery()
     */
    public function getQuery(): string {
        return $this->query;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getScheme()
     */
    public function getScheme(): string {
        return $this->scheme;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::getUserInfo()
     */
    public function getUserInfo(): string {
        return $this->userInfo;
    }

    /**
     * Determine port is default for scheme
     *
     * @param int $port
     * @param string $scheme
     * @return bool
     */
    protected function isStandardPort(int $port, string $scheme): bool {
        return $port === $this->defaultSchemePorts[$scheme] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setFragment($fragment)
     */
    public function setFragment($fragment) {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setHost($host)
     */
    public function setHost(string $host) {
        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setPath($path)
     */
    public function setPath(string $path) {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setPort($port)
     */
    public function setPort($port) {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setQuery($query)
     */
    public function setQuery(string $query) {
        $this->query = $query;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setScheme($scheme)
     */
    public function setScheme(string $scheme) {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Uri\UriInterface::setUserInfo($userInfo)
     */
    public function setUserInfo(string $userInfo) {
        $this->userInfo = $userInfo;

        return $this;
    }
}