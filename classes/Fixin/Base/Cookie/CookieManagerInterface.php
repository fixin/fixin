<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\PrototypeInterface;

interface CookieManagerInterface extends PrototypeInterface {

    const OPTION_COOKIES = 'cookies';

    /**
     * Expire cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @return self
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface;

    /**
     * Get cookie value
     *
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function getValue(string $name, string $default = null);

    /**
     * Determine has value
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Send changed or new cookies
     *
     * @return self
     */
    public function sendChanges(): self;

    /**
     * Set cookie
     *
     * @param string $name
     * @param string $value
     * @return CookieInterface
     */
    public function set(string $name, string $value): CookieInterface;
}