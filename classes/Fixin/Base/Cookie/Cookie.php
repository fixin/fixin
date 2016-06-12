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

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var integer
     */
    protected $expire = 0;

    /**
     * @var string
     */
    protected $httpOnly = false;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $secure = false;

    /**
     * @var string
     */
    protected $value = '';

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieInterface::sendAs($name)
     */
    public function sendAs(string $name): CookieInterface {
        if (setcookie($name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly)) {
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