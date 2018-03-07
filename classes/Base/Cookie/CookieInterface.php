<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\PrototypeInterface;

interface CookieInterface extends PrototypeInterface
{
    public const
        DOMAIN = 'domain',
        EXPIRE_TIME = 'expireTime',
        HTTP_ONLY = 'httpOnly',
        PATH = 'path',
        SECURE = 'secure',
        VALUE = 'value';

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain(): string;

    /**
     * Get expire time in seconds
     *
     * @return int
     */
    public function getExpireTime(): int;

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get value
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Determine if HTTP-only
     *
     * @return bool
     */
    public function isHttpOnly(): bool;

    /**
     * Determine if secure
     *
     * @return bool
     */
    public function isSecure(): bool;

    /**
     * Send as
     *
     * @param string $name
     * @param int $baseTime
     * @return $this
     */
    public function sendAs(string $name, int $baseTime): CookieInterface;

    /**
     * Set domain
     *
     * @param string $domain
     * @return $this
     */
    public function setDomain(string $domain): CookieInterface;

    /**
     * Set expire time in seconds
     *
     * @param int $expireTime
     * @return $this
     */
    public function setExpireTime(int $expireTime): CookieInterface;

    /**
     * Set HTTP only
     *
     * @param bool $httpOnly
     * @return $this
     */
    public function setHttpOnly(bool $httpOnly): CookieInterface;

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): CookieInterface;

    /**
     * Set secure
     *
     * @param bool $secure
     * @return  $this
     */
    public function setSecure(bool $secure): CookieInterface;

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): CookieInterface;
}
