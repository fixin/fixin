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

    public function getDomain(): string;
    public function getExpireTime(): int;
    public function getPath(): string;
    public function getValue(): string;
    public function isHttpOnly(): bool;
    public function isSecure(): bool;

    /**
     * @return $this
     */
    public function sendAs(string $name, int $baseTime): CookieInterface;

    /**
     * @return $this
     */
    public function setDomain(string $domain): CookieInterface;

    /**
     * @return $this
     */
    public function setExpireTime(int $expireTime): CookieInterface;

    /**
     * @return $this
     */
    public function setHttpOnly(bool $httpOnly): CookieInterface;

    /**
     * @return $this
     */
    public function setPath(string $path): CookieInterface;

    /**
     * @return $this
     */
    public function setSecure(bool $secure): CookieInterface;

    /**
     * @return $this
     */
    public function setValue(string $value): CookieInterface;
}
