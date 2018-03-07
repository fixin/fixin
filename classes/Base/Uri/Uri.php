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
use Fixin\Support\Types;

class Uri extends Prototype implements UriInterface
{
    protected const
        THIS_SETS = [
            self::FRAGMENT => [Types::STRING, Types::NULL],
            self::HOST => Types::STRING,
            self::PATH => Types::STRING,
            self::PORT => [Types::INT, Types::NULL],
            self::QUERY => Types::STRING,
            self::SCHEME => Types::STRING,
            self::USER_INFO => Types::STRING
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

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return ltrim($this->scheme . '://', ':/')
            . $this->getAuthority()
            . ($this->path !== '' ? '/' . ltrim($this->path, '/') : '')
            . rtrim('?' . $this->query, '?')
            . rtrim('#' . $this->fragment, '#');
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * Determine port is default for scheme
     *
     * @param int|null $port
     * @param string $scheme
     * @return bool
     */
    protected function isStandardPort(?int $port, string $scheme): bool
    {
        return $port === $this->defaultSchemePorts[$scheme] ?? false;
    }
}
