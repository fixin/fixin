<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Model\RepositoryInterface;
use Fixin\Exception\RuntimeException;
use Fixin\Resource\Prototype;
use Fixin\Resource\Resource;
use Fixin\Support\Strings;

class SessionManager extends Prototype implements SessionManagerInterface {

    const COLUMN_IN = 'id';
    const EXCEPTION_COOKIE_NAME_NOT_SET = 'Cookie name not set';
    const EXCEPTION_COOKIE_MANAGER_NOT_SET = 'Cookie manager not set';
    const EXCEPTION_REPOSITORY_NOT_SET = 'Repository not set';

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
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (!isset($this->repository)) {
            throw new RuntimeException(static::EXCEPTION_REPOSITORY_NOT_SET);
        }

        if (!isset($this->cookieManager)) {
            throw new RuntimeException(static::EXCEPTION_COOKIE_MANAGER_NOT_SET);
        }

        if ($this->cookieName === '') {
            throw new RuntimeException(static::EXCEPTION_COOKIE_NAME_NOT_SET);
        }

        return $this;
    }

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
        $this->getCookieManager()->set($this->cookieName, $this->sessionId, $this->lifetime);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::start()
     */
    public function start(): SessionManagerInterface {
        if (!$this->started) {
            $this->started = true;

            if ($sessionId = $this->getCookieManager()->getValue($this->cookieName)) {
                if ($entity = $this->getRepository()->get([static::COLUMN_IN => $sessionId])) {
                    $this->areas = $entity->getData();

                    $this->sessionId = $sessionId;
                    $this->lifetime && $this->setupCookie();

                    return $this;
                }
            }

            $this->regenerateId();
        }

        return $this;
    }
}