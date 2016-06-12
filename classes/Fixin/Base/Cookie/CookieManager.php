<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;

class CookieManager extends Prototype implements CookieManagerInterface {

    const EXPIRE_MINUTES = -24 * 60 * 7;

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::expire($name, $path, $domain)
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface {
        return $this->set($name, null, static::EXPIRE_MINUTES, $path, $domain);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::get()
     */
    public function getValue(string $name, string $default = null) {
        return isset($this->cookies[$name]) ? (($item = $this->cookies[$name])  instanceof CookieInterface ? $item->getValue() : $item) : $default;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::has($name)
     */
    public function has(string $name): bool {
        return isset($this->cookies[$name]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::sendChanges()
     */
    public function sendChanges(): CookieManagerInterface {
        foreach ($this->cookies as $name => $cookie) {
            if ($cookie instanceof CookieInterface) {
                $cookie->sendAs($name);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Cookie\CookieManagerInterface::set($name, $value)
     */
    public function set(string $name, string $value): CookieInterface {
        if (!isset($this->cookies[$name]) || !($cookie = $this->cookies[$name]) instanceof CookieInterface) {
            $cookie =
            $this->cookies[$name] = $this->container->clonePrototype('Base\Cookie\Cookie');
        }

        return $cookie->setValue($value);
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