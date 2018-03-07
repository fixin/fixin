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
        EXPIRE_SECONDS = -7 * 24 * 60  * 60,
        THIS_SETS = [
            self::COOKIES => Types::ARRAY,
        ];

    /**
     * @var string[]|CookieInterface[]
     */
    protected $cookies = [];

    /**
     * @inheritDoc
     */
    public function __debugInfo()
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function expire(string $name, string $path = '', string $domain = ''): CookieManagerInterface
    {
        $this->set($name, '')
            ->setExpireTime(static::EXPIRE_SECONDS)
            ->setPath($path)
            ->setDomain($domain);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, string $default = null): ?string
    {
        return isset($this->cookies[$name]) ? (($item = $this->cookies[$name]) instanceof CookieInterface ? $item->getValue() : $item) : $default;
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    public function set(string $name, string $value): CookieInterface
    {
        if (!isset($this->cookies[$name]) || !($cookie = $this->cookies[$name]) instanceof CookieInterface) {
            $cookie =
            $this->cookies[$name] = $this->resourceManager->clone('*\Base\Cookie\Cookie', CookieInterface::class);
        }

        return $cookie->setValue($value);
    }
}
