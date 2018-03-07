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

interface CookieManagerInterface extends PrototypeInterface
{
    public const
        COOKIES = 'cookies';

    /**
     * Expire cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @return $this
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface;

    /**
     * Get cookie value
     *
     * @param string $name
     * @param string|null $default
     * @return null|string
     */
    public function get(string $name, string $default = null): ?string;

    /**
     * Determine if has cookie with given name
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Send changed and new cookies
     *
     * @return $this
     */
    public function sendChanges(): CookieManagerInterface;

    /**
     * Set cookie value
     *
     * @param string $name
     * @param string $value
     * @return CookieInterface
     */
    public function set(string $name, string $value): CookieInterface;
}
