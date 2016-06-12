<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Model\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Strings;

class SessionManager extends Prototype implements SessionManagerInterface {

    const COLUMN_IN = 'id';
    const CONFIGURATION_REQUIRES = [
        'cookieManager' => 'instance',
        'cookieName' => 'string',
        'repository' => 'instance',
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
        return $this->cookieManager ?: $this->loadLazyProperty('cookieManager');
    }

    /**
     * Get repository instance
     *
     * @return RepositoryInterface
     */
    protected function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty('repository');
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
     * Set cookie manager
     *
     * @param string|CookieManagerInterface $cookieManager
     */
    protected function setCookieManager($cookieManager) {
        $this->setLazyLoadingProperty('cookieManager', CookieManagerInterface::class, $cookieManager);
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
     * Set repository
     *
     * @param string|RepositoryInterface $repository
     */
    protected function setRepository($repository) {
        $this->setLazyLoadingProperty('repository', RepositoryInterface::class, $repository);
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
        if ($entity = $this->getRepository()->get([static::COLUMN_IN => $sessionId])) {
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