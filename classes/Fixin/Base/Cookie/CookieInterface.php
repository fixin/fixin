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
        OPTION_DOMAIN = 'domain',
        OPTION_EXPIRE_TIME = 'expireTime',
        OPTION_HTTP_ONLY = 'httpOnly',
        OPTION_PATH = 'path',
        OPTION_SECURE = 'secure',
        OPTION_VALUE = 'value';

    public function getDomain(): string;

    /**
     * Get expire in minutes
     */
    public function getExpireTime(): int;

    public function getPath(): string;
    public function getValue(): string;
    public function isHttpOnly(): bool;
    public function isSecure(): bool;
    public function sendAs(string $name): CookieInterface;
    public function setDomain(string $domain): CookieInterface;

    /**
     * Set expire in minutes
     */
    public function setExpireTime(int $expireTime): CookieInterface;

    public function setHttpOnly(bool $httpOnly): CookieInterface;
    public function setPath(string $path): CookieInterface;
    public function setSecure(bool $secure): CookieInterface;
    public function setValue(string $value): CookieInterface;
}
