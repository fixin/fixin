<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\PrototypeInterface;

interface CookieManagerInterface extends PrototypeInterface
{
    public const
        OPTION_COOKIES = 'cookies';

    /**
     * Expire cookie
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface;

    /**
     * Get cookie value
     */
    public function getValue(string $name, string $default = null): ?string;

    /**
     * Determine has value
     */
    public function has(string $name): bool;

    /**
     * Send changed or new cookies
     */
    public function sendChanges(): CookieManagerInterface;

    /**
     * Set cookie
     */
    public function set(string $name, string $value): CookieInterface;
}
