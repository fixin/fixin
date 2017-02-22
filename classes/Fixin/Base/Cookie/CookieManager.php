<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;

class CookieManager extends Prototype implements CookieManagerInterface
{
    protected const
        EXPIRE_MINUTES = -24 * 60 * 7;

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @return static
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface
    {
        $this->set($name, null)->setExpire(static::EXPIRE_MINUTES)->setPath($path)->setDomain($domain);

        return $this;
    }

    public function getValue(string $name, string $default = null): ?string
    {
        return isset($this->cookies[$name]) ? (($item = $this->cookies[$name]) instanceof CookieInterface ? $item->getValue() : $item) : $default;
    }

    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * @return static
     */
    public function sendChanges(): CookieManagerInterface
    {
        foreach ($this->cookies as $name => $cookie) {
            if ($cookie instanceof CookieInterface) {
                $cookie->sendAs($name);
            }
        }

        return $this;
    }

    public function set(string $name, string $value): CookieInterface
    {
        if (!isset($this->cookies[$name]) || !($cookie = $this->cookies[$name]) instanceof CookieInterface) {
            $cookie =
            $this->cookies[$name] = $this->container->clonePrototype('Base\Cookie\Cookie');
        }

        return $cookie->setValue($value);
    }

    protected function setCookies(array $cookies): void
    {
        $this->cookies = $cookies;
    }
}
