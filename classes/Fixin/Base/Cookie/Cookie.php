<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;
use Fixin\Exception\RuntimeException;

class Cookie extends Prototype implements CookieInterface {

    const EXCEPTION_CAN_T_SET_COOKIE = "Can't set '%s' cookie";
    const EXPIRE_TO_TIMESTAMP = 60;

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

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::getDomain()
     */
    public function getDomain(): string {
        return $this->domain;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::getExpire()
     */
    public function getExpire(): int {
        return $this->expire;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::getPath()
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::getValue()
     */
    public function getValue(): string {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::isHttpOnly()
     */
    public function isHttpOnly(): bool {
        return $this->httpOnly;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::isSecure()
     */
    public function isSecure(): bool {
        return $this->secure;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::sendAs($name)
     */
    public function sendAs(string $name): CookieInterface {
        if (setcookie($name, $this->value, $this->expire ? time() + $this->expire * static::EXPIRE_TO_TIMESTAMP : 0, $this->path, $this->domain, $this->secure, $this->httpOnly)) {
            return $this;
        }

        throw new RuntimeException(sprintf(static::EXCEPTION_CAN_T_SET_COOKIE, $name));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::setDomain($domain)
     */
    public function setDomain(string $domain): CookieInterface {
        $this->domain = $domain;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::setExpire($expire)
     */
    public function setExpire(int $expire): CookieInterface {
        $this->expire = $expire;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::setHttpOnly($httpOnly)
     */
    public function setHttpOnly(bool $httpOnly): CookieInterface {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::setPath($path)
     */
    public function setPath(string $path): CookieInterface {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::setSecure($secure)
     */
    public function setSecure(bool $secure): CookieInterface {
        $this->secure = $secure;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::setValue($value)
     */
    public function setValue(string $value): CookieInterface {
        $this->value = $value;

        return $this;
    }
}