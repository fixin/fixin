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
     * @return $this
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface;

    public function get(string $name, string $default = null): ?string;
    public function has(string $name): bool;

    /**
     * @return $this
     */
    public function sendChanges(): CookieManagerInterface;

    public function set(string $name, string $value): CookieInterface;
}
