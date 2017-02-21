<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;

class Cookie extends Prototype implements CookieInterface
{
    protected const
        EXCEPTION_CAN_T_SET_COOKIE = "Can't set '%s' cookie",
        EXPIRE_TO_TIMESTAMP = 60;

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var int
     */
    protected $expire = 0;

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

    public function getExpire(): int
    {
        return $this->expire;
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
     * @return static
     */
    public function sendAs(string $name): CookieInterface
    {
        if (setcookie($name, $this->value, $this->expire ? time() + $this->expire * static::EXPIRE_TO_TIMESTAMP : 0, $this->path, $this->domain, $this->secure, $this->httpOnly)) {
            return $this;
        }

        throw new RuntimeException(sprintf(static::EXCEPTION_CAN_T_SET_COOKIE, $name));
    }

    /**
     * @return static
     */
    public function setDomain(string $domain): CookieInterface
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return static
     */
    public function setExpire(int $expire): CookieInterface
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * @return static
     */
    public function setHttpOnly(bool $httpOnly): CookieInterface
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * @return static
     */
    public function setPath(string $path): CookieInterface
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return static
     */
    public function setSecure(bool $secure): CookieInterface
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @return static
     */
    public function setValue(string $value): CookieInterface
    {
        $this->value = $value;

        return $this;
    }
}
