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

    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface;
    public function getValue(string $name, string $default = null): ?string;
    public function has(string $name): bool;

    /**
     * Send changed or new cookies
     */
    public function sendChanges(): CookieManagerInterface;

    public function set(string $name, string $value): CookieInterface;
}
