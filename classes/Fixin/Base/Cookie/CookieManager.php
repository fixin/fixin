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
     * @see \Fixin\Base\Cookie\CookieManagerInterface::set($name, $value, $expire, $path, $domain, $secure, $httpOnly)
     */
    public function set(string $name, string $value, int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httpOnly = false): CookieManagerInterface {
        if (!isset($this->cookies[$name]) || !($cookie = $this->cookies[$name]) instanceof CookieInterface) {
            $cookie =
            $this->cookies[$name] = $this->container->clonePrototype('Base\Cookie\Cookie');
        }

        $cookie
        ->setValue($value)
        ->setExpire($expire)
        ->setPath($path)
        ->setDomain($domain)
        ->setSecure($secure)
        ->setHttpOnly($httpOnly);

        return $this;
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