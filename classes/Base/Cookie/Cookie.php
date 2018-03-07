<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;

class Cookie extends Prototype implements CookieInterface
{
    protected const
        SET_FAILURE_EXCEPTION = "Can't set '%s' cookie",
        THIS_SETS = [
            self::DOMAIN => self::USING_SETTER,
            self::EXPIRE_TIME => self::USING_SETTER,
            self::HTTP_ONLY => self::USING_SETTER,
            self::PATH => self::USING_SETTER,
            self::SECURE => self::USING_SETTER,
            self::VALUE => self::USING_SETTER
        ];

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var int
     */
    protected $expireTime = 0;

    /**
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @inheritDoc
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @inheritDoc
     */
    public function getExpireTime(): int
    {
        return $this->expireTime;
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @inheritDoc
     */
    public function sendAs(string $name, int $baseTime): CookieInterface
    {
        if (setcookie($name, $this->value, $this->expireTime ? $baseTime + $this->expireTime : 0, $this->path, $this->domain, $this->secure, $this->httpOnly)) {
            return $this;
        }

        throw new Exception\SetFailureException(sprintf(static::SET_FAILURE_EXCEPTION, $name));
    }

    /**
     * @inheritDoc
     */
    public function setDomain(string $domain): CookieInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setExpireTime(int $expireTime): CookieInterface
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHttpOnly(bool $httpOnly): CookieInterface
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path): CookieInterface
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSecure(bool $secure): CookieInterface
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue(string $value): CookieInterface
    {
        $this->value = $value;

        return $this;
    }
}
