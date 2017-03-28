<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Uri;

use Fixin\Resource\Prototype;

class Uri extends Prototype implements UriInterface
{
    protected const
        THIS_REQUIRES = [
            self::HOST,
            self::PATH,
            self::QUERY,
        ],
        THIS_SETS = [
            self::FRAGMENT => [self::STRING_TYPE, self::NULL_TYPE],
            self::HOST => self::STRING_TYPE,
            self::PATH => self::STRING_TYPE,
            self::PORT => [self::INT_TYPE, self::NULL_TYPE],
            self::QUERY => self::STRING_TYPE,
            self::SCHEME => self::STRING_TYPE,
            self::USER_INFO => self::STRING_TYPE
        ];

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
            . ($this->path !== null ? '/' . ltrim($this->path, '/') : '')
            . rtrim('?' . $this->query, '?')
            . rtrim('#' . $this->fragment, '#');
    }

    public function getAuthority(): ?string
    {
        // Host
        $authority = $this->host;

        if ($authority === '') {
            return null;
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
}
