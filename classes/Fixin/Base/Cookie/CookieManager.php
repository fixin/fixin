<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;

class CookieManager extends Prototype implements CookieManagerInterface {

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::get()
     */
    public function getValue(string $name) {
        $item = $this->cookies[$name] ?? null;

        return $item instanceof CookieInterface ? $item->getValue() : $item;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::has($name)
     */
    public function has(string $name): bool {
        return isset($this->cookies[$name]);
    }

    /**
     * Set cookies
     *
     * @param array $cookies
     */
    protected function setCookies(array $cookies) {
        $this->cookies = $cookies;
    }
}