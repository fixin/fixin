<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class CookieManager extends Prototype implements CookieManagerInterface
{
    protected const
        EXPIRE_MINUTES = -24 * 60 * 7,
        THIS_SETS = [
            self::COOKIES => Types::ARRAY,
        ];

    /**
     * @var string[]|CookieInterface[]
     */
    protected $cookies = [];

    public function __debugInfo()
    {
        return $this->cookies;
    }

    /**
     * @return $this
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface
    {
        $this->set($name, null)
            ->setExpireTime(static::EXPIRE_MINUTES)
            ->setPath($path)
            ->setDomain($domain);

        return $this;
    }

    public function get(string $name, string $default = null): ?string
    {
        return isset($this->cookies[$name]) ? (($item = $this->cookies[$name]) instanceof CookieInterface ? $item->getValue() : $item) : $default;
    }

    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * @return $this
     */
    public function sendChanges(): CookieManagerInterface
    {
        $baseTime = time();

        foreach ($this->cookies as $name => $cookie) {
            if ($cookie instanceof CookieInterface) {
                $cookie->sendAs($name, $baseTime);

                $this->cookies[$name] = $cookie->getValue();
            }
        }

        return $this;
    }

    public function set(string $name, string $value): CookieInterface
    {
        if (!isset($this->cookies[$name]) || !($cookie = $this->cookies[$name]) instanceof CookieInterface) {
            $cookie =
            $this->cookies[$name] = $this->resourceManager->clone('Base\Cookie\Cookie', CookieInterface::class);
        }

        return $cookie->setValue($value);
    }
}
