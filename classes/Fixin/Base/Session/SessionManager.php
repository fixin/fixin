<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use DateTime;
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
     * @var SessionEntity
     */
    protected $entity;

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
        return $this->areas[$name] = (new \Fixin\Base\Session\SessionArea())->setModified(true);
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
     * @see \Fixin\Base\Session\SessionManagerInterface::isModified()
     */
    public function isModified(): bool {
        foreach ($this->areas as $area) {
            if ($area->isModified()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::regenerateId()
     */
    public function regenerateId(): SessionManagerInterface {
        $this->sessionId = $this->generateId();
        $this->entity->setSessionId($this->sessionId);

        $this->setupCookie();

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::save()
     */
    public function save(): SessionManagerInterface {
        if ($this->started && $this->isModified()) {
            $this->entity
            ->setData($this->areas)
            ->setAccessTime(new DateTime())
            ->save();

            foreach ($this->areas as $area) {
                $area->setModified(false);
            }
        }

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

            // New session
            $this->areas = [];
            $this->entity = $this->getRepository()->create();
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
        $repository = $this->getRepository();
        $request = $repository->createRequest();
        $where = $request->getWhere()->compare(SessionEntity::COLUMN_SESSION_ID, '=', $sessionId);

        if ($this->lifetime) {
            $where->compare(SessionEntity::COLUMN_ACCESS_TIME, '>=', new DateTime('+' . $this->lifetime . ' MINUTES'));
        }

        $this->entity = $request->fetchFirst();

        if (isset($this->entity)) {
            $this->areas = $this->entity->getData();
            $this->sessionId = $sessionId;

            if ($this->lifetime) {
                $this->setupCookie();
            }

            $request = $repository->createRequest();
            $request->getWhere()->compare(SessionEntity::COLUMN_SESSION_ID, '=', $sessionId);
            $request->update([
                SessionEntity::COLUMN_ACCESS_TIME => new DateTime()
            ]);

            return true;
        }

        return false;
    }
}