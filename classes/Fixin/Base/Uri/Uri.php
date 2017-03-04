<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri;

use Fixin\Resource\Prototype;

class Uri extends Prototype implements UriInterface
{
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

    public function __toString(): string
    {
        return ltrim($this->scheme . '://', ':/')
            . $this->getAuthority()
            . ($this->path !== '' ? '/' . ltrim($this->path, '/') : '')
            . rtrim('?' . $this->query, '?')
            . rtrim('#' . $this->fragment, '#');
    }

    public function getAuthority(): string
    {
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

    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * Determine port is default for scheme
     */
    protected function isStandardPort(int $port, string $scheme): bool
    {
        return $port === $this->defaultSchemePorts[$scheme] ?? null;
    }

    protected function setFragment(?string $fragment): void
    {
        $this->fragment = $fragment;
    }

    protected function setHost(string $host): void
    {
        $this->host = $host;
    }

    protected function setPath(string $path): void
    {
        $this->path = $path;
    }

    protected function setPort(?int $port): void
    {
        $this->port = $port;
    }

    protected function setQuery(string $query): void
    {
        $this->query = $query;
    }

    protected function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }

    protected function setUserInfo(string $userInfo): void
    {
        $this->userInfo = $userInfo;
    }
}
