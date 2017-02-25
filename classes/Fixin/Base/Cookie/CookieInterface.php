<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\PrototypeInterface;

interface CookieInterface extends PrototypeInterface
{
    public const
        OPTION_DOMAIN = 'domain',
        OPTION_EXPIRE = 'expire',
        OPTION_HTTP_ONLY = 'httpOnly',
        OPTION_PATH = 'path',
        OPTION_SECURE = 'secure',
        OPTION_VALUE = 'value';

    public function getDomain(): string;

    /**
     * Get expire in minutes
     */
    public function getExpire(): int;

    public function getPath(): string;
    public function getValue(): string;

    /**
     * Determine if HTTP-only
     */
    public function isHttpOnly(): bool;

    /**
     * Determine if secure
     */
    public function isSecure(): bool;

    public function sendAs(string $name): CookieInterface;
    public function setDomain(string $domain): CookieInterface;

    /**
     * Set expire in minutes
     */
    public function setExpire(int $expire): CookieInterface;

    public function setHttpOnly(bool $httpOnly): CookieInterface;
    public function setPath(string $path): CookieInterface;
    public function setSecure(bool $secure): CookieInterface;
    public function setValue(string $value): CookieInterface;
}
