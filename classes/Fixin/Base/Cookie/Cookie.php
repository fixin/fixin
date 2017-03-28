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
        EXPIRE_TO_TIMESTAMP = 60,
        SET_FAILURE_EXCEPTION = "Can't set '%s' cookie";

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

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getExpireTime(): int
    {
        return $this->expireTime;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @return $this
     * @throws Exception\RuntimeException
     */
    public function sendAs(string $name, int $baseTime): CookieInterface
    {
        if (setcookie($name, $this->value, $this->expireTime ? $baseTime + $this->expireTime * static::EXPIRE_TO_TIMESTAMP : 0, $this->path, $this->domain, $this->secure, $this->httpOnly)) {
            return $this;
        }

        throw new Exception\RuntimeException(sprintf(static::SET_FAILURE_EXCEPTION, $name));
    }

    /**
     * @return $this
     */
    public function setDomain(string $domain): CookieInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return $this
     */
    public function setExpireTime(int $expireTime): CookieInterface
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * @return $this
     */
    public function setHttpOnly(bool $httpOnly): CookieInterface
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPath(string $path): CookieInterface
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSecure(bool $secure): CookieInterface
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @return $this
     */
    public function setValue(string $value): CookieInterface
    {
        $this->value = $value;

        return $this;
    }
}
