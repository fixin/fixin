<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Strings;

class SessionManager extends Prototype implements SessionManagerInterface {

    const THIS_REQUIRES = [
        self::OPTION_COOKIE_MANAGER => self::TYPE_INSTANCE,
        self::OPTION_COOKIE_NAME => self::TYPE_STRING,
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ];
    const THIS_SETS_LAZY = [
        self::OPTION_COOKIE_MANAGER => CookieManagerInterface::class,
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var SessionAreaInterface[]
     */
    protected $areas = [];

    /**
     * @var CookieManagerInterface|false|null
     */
    protected $cookieManager;

    /**
     * @var string
     */
    protected $cookieName = 'session';

    /**
     * @var integer
     */
    protected $lifetime = 0;

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * Generate session id
     *
     * @return string
     */
    protected function generateId(): string {
        return sha1(Strings::generateRandom(24) . uniqid('', true) . microtime(true));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getArea($name)
     */
    public function getArea(string $name): SessionAreaInterface {
        $this->start();

        // Existing area
        if (isset($this->areas[$name])) {
            return $this->areas[$name];
        }

        // New area
        return $this->areas[$name] = $this->container->clonePrototype('Base\Session\SessionArea');
    }

    /**
     * Get cookie manager instance
     *
     * @return CookieManagerInterface
     */
    protected function getCookieManager(): CookieManagerInterface {
        return $this->cookieManager ?: $this->loadLazyProperty(static::OPTION_COOKIE_MANAGER);
    }

    /**
     * Get repository instance
     *
     * @return RepositoryInterface
     */
    protected function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::regenerateId()
     */
    public function regenerateId(): SessionManagerInterface {
        $this->sessionId = $this->generateId();

        $this->setupCookie();

        return $this;
    }

    /**
     * Set cookie name
     *
     * @param string $cookieName
     */
    protected function setCookieName(string $cookieName) {
        $this->cookieName = $cookieName;
    }

    /**
     * Set lifetime
     *
     * @param int $lifetime
     */
    protected function setLifetime(int $lifetime) {
        $this->lifetime = $lifetime;
    }

    /**
     * Setup cookie
     */
    protected function setupCookie() {
        $this->getCookieManager()->set($this->cookieName, $this->sessionId)->setExpire($this->lifetime)->setPath('/');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::start()
     */
    public function start(): SessionManagerInterface {
        if (!$this->started) {
            $this->started = true;

            $sessionId = $this->getCookieManager()->getValue($this->cookieName);
            if ($sessionId && $this->startWith($sessionId)) {
                return $this;
            }

            $this->regenerateId();
        }

        return $this;
    }

    /**
     * Start with stored session id
     *
     * @param string $sessionId
     * @return bool
     */
    protected function startWith(string $sessionId): bool {
        if ($entity = $this->getRepository()->createId($sessionId)->getEntity()) {
            $this->areas = $entity->getData();

            $this->sessionId = $sessionId;
            if ($this->lifetime) {
                $this->setupCookie();
            }

            return true;
        }

        return false;
    }
}