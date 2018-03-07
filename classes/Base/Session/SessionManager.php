<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Session;

use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Dictionary\DictionaryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Strings;
use Fixin\Support\Types;


class SessionManager extends Prototype implements SessionManagerInterface
{
    protected const
        DATA_REGENERATED = 'regenerated',
        THIS_SETS = [
            self::COOKIE_MANAGER => [self::LAZY_LOADING => CookieManagerInterface::class],
            self::COOKIE_NAME => Types::STRING,
            self::KEY_PREFIX => Types::STRING,
            self::LIFETIME => Types::INT,
            self::REGENERATION_FORWARD_TIME => Types::INT,
            self::STORE => [self::LAZY_LOADING => DictionaryInterface::class]
        ];

    /**
     * @var SessionAreaInterface[]
     */
    protected $areas;

    /**
     * @var CookieManagerInterface|false|null
     */
    protected $cookieManager;

    /**
     * @var string
     */
    protected $cookieName = 'session';

    /**
     * @var string
     */
    protected $keyPrefix = 'session.';

    /**
     * @var integer
     */
    protected $lifetime = 0;

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @var integer
     */
    protected $regenerationForwardTime = 60;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var DictionaryInterface
     */
    protected $store;

    /**
     * @inheritDoc
     */
    public function clear(): SessionManagerInterface
    {
        $this->started || $this->start();

        $this->areas = [];
        $this->modified = true;

        return $this;
    }

    /**
     * Generate ID
     *
     * @return string
     */
    protected function generateId(): string
    {
        // TODO: revision
        return sha1(Strings::generateRandomAlnum(24) . uniqid('', true) . microtime(true));
    }

    /**
     * @inheritDoc
     */
    public function getArea(string $name): SessionAreaInterface
    {
        $this->started || $this->start();

        // Existing area
        if (isset($this->areas[$name])) {
            return $this->areas[$name];
        }

        // New area
        $this->modified = true;

        return $this->areas[$name] = $this->resourceManager->clone('*\Base\Session\SessionArea', SessionAreaInterface::class);
    }

    /**
     * Get the cookie manager
     *
     * @return CookieManagerInterface
     */
    protected function getCookieManager(): CookieManagerInterface
    {
        return $this->cookieManager ?: $this->loadLazyProperty(static::COOKIE_MANAGER);
    }

    /**
     * @inheritDoc
     */
    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * @inheritDoc
     */
    public function getRegenerationForwardTime(): int
    {
        return $this->regenerationForwardTime;
    }

    /**
     * Get store
     *
     * @return DictionaryInterface
     */
    protected function getStore(): DictionaryInterface
    {
        return $this->store ?: $this->loadLazyProperty(static::STORE);
    }

    /**
     * @inheritDoc
     */
    public function isModified(): bool
    {
        $this->started || $this->start();

        if ($this->modified) {
            return true;
        }

        foreach ($this->areas as $area) {
            if ($area->isModified()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function regenerateId(): SessionManagerInterface
    {
        $this->started || $this->start();

        $sessionId = $this->generateId();
        $this->modified = true;

        if ($this->sessionId) {
            $this->getStore()->set($this->keyPrefix . $this->sessionId, [static::DATA_REGENERATED => $sessionId], $this->regenerationForwardTime);
        }

        $this->sessionId = $sessionId;
        $this->save();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(): SessionManagerInterface
    {
        if ($this->started && $this->isModified()) {
            $this->getStore()->set($this->keyPrefix . $this->sessionId, $this->areas, $this->lifetime);

            $this->setupCookie();

            $this->modified = true;

            foreach ($this->areas as $area) {
                $area->setModified(false);
            }
        }

        return $this;
    }

    /**
     * Setup cookie
     */
    protected function setupCookie(): void
    {
        $this->getCookieManager()->set($this->cookieName, $this->sessionId)
            ->setExpireTime($this->lifetime)
            ->setPath('/');
    }

    /**
     * @inheritDoc
     */
    public function start(): SessionManagerInterface
    {
        if ($this->started) {
            return $this;
        }

        $this->started = true;

        if (($sessionId = $this->getCookieManager()->get($this->cookieName)) && $this->startWithId($sessionId)) {
            return $this;
        }

        // New session
        $this->areas = [];

        $this->regenerateId();

        return $this;
    }

    /**
     * Start session with ID
     *
     * @param string $sessionId
     * @return bool
     */
    protected function startWithId(string $sessionId): bool
    {
        if (null === $data = $this->getStore()->get($this->keyPrefix . $sessionId)) {
            return false;
        }

        // Regenerated
        if (isset($data[static::DATA_REGENERATED])) {
            return $this->startWithId($data[static::DATA_REGENERATED]);
        }

        // Restore areas
        $areas = [];

        /** @var $area SessionAreaInterface */
        foreach ($data as $key => $area) {
            $areas[$key] = $area->withResourceManager($this->resourceManager);
        }

        $this->areas = $areas;
        $this->sessionId = $sessionId;

        $this->setupCookie();

        return true;
    }
}
