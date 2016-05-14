<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri;

class Uri implements UriInterface {

    /**
     * @var string
     */
    protected $fragment = '';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var unknown
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
}