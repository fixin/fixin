<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Session;

use DateTime;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Strings;

class SessionManager extends Prototype implements SessionManagerInterface
{
    protected const
        DATA_REGENERATED = 'regenerated',
        THIS_REQUIRES = [
            self::OPTION_COOKIE_MANAGER,
            self::OPTION_COOKIE_NAME,
            self::OPTION_REPOSITORY
        ],
        THIS_SETS_LAZY = [
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
     * @var bool
     */
    protected $modified = false;

    /**
     * @var integer
     */
    protected $regenerationForwardTime = 1;

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
     * @return static
     */
    public function clear(): SessionManagerInterface
    {
        $this->start();

        $this->areas = [];
        $this->modified = true;

        return $this;
    }

    public function deleteGarbageSessions(int $lifetime): int
    {
        $request = $this->getRepository()->createRequest();
        $request->getWhere()->compare(SessionEntity::ACCESS_TIME, '<', new DateTime('+' . $lifetime . ' MINUTES'));

        return $request->delete();
    }

    protected function generateId(): string
    {
        return sha1(Strings::generateRandomAlnum(24) . uniqid('', true) . microtime(true));
    }

    public function getArea(string $name): SessionAreaInterface
    {
        $this->start();

        // Existing area
        if (isset($this->areas[$name])) {
            return $this->areas[$name];
        }

        // New area
        $this->modified = true;

        return $this->areas[$name] = $this->container->clone('Base\Session\SessionArea');
    }

    protected function getCookieManager(): CookieManagerInterface
    {
        return $this->cookieManager ?: $this->loadLazyProperty(static::OPTION_COOKIE_MANAGER);
    }

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function getRegenerationForwardTime(): int
    {
        return $this->regenerationForwardTime;
    }

    protected function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    public function isModified(): bool
    {
        $this->start();

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

    protected function loadEntity(SessionEntity $entity): void
    {
        $this->entity = $entity;
        $this->areas = $entity->getData();
        $this->sessionId = $entity->getSessionId();

        if ($this->lifetime) {
            $this->setupCookie();
        }

        $request = $this->getRepository()->createRequest();
        $request->getWhere()->compare(SessionEntity::SESSION_ID, '=', $this->sessionId);
        $request->update([SessionEntity::ACCESS_TIME => new DateTime()]);
    }

    /**
     * @return static
     */
    public function regenerateId(): SessionManagerInterface
    {
        $this->start();

        $this->sessionId = $this->generateId();
        $this->modified = true;

        if ($this->entity->isStored()) {
            $this->entity
            ->setData([static::DATA_REGENERATED => $this->sessionId])
            ->setAccessTime(new DateTime())
            ->save();

            $this->entity = $this->getRepository()->create();
        }

        $this->setupCookie();

        return $this;
    }

    /**
     * @return static
     */
    public function save(): SessionManagerInterface
    {
        if ($this->started && $this->isModified()) {
            $this->entity
                ->setSessionId($this->sessionId)
                ->setData($this->areas)
                ->setAccessTime(new DateTime())
                ->save();

            $this->modified = false;

            foreach ($this->areas as $area) {
                $area->clearModified();
            }
        }

        return $this;
    }

    protected function setCookieName(string $cookieName): void
    {
        $this->cookieName = $cookieName;
    }

    protected function setLifetime(int $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    protected function setRegenerationForwardTime(int $regenerationForwardTime): void
    {
        $this->regenerationForwardTime = $regenerationForwardTime;
    }

    protected function setupCookie(): void
    {
        $this->getCookieManager()->set($this->cookieName, $this->sessionId)->setExpire($this->lifetime)->setPath('/');
    }

    /**
     * @return static
     */
    public function start(): SessionManagerInterface
    {
        if (!$this->started) {
            $this->started = true;

            $sessionId = $this->getCookieManager()->get($this->cookieName);
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
     */
    protected function startWith(string $sessionId): bool
    {
        $request = $this->getRepository()->createRequest();
        $where = $request->getWhere()->compare(SessionEntity::SESSION_ID, '=', $sessionId);

        if ($this->lifetime) {
            $where->compare(SessionEntity::ACCESS_TIME, '>=', new DateTime('+' . $this->lifetime . ' MINUTES'));
        }

        /** @var SessionEntity $entity */
        $entity = $request->fetchFirst();

        if ($entity) {
            $data = $entity->getData();
            if (isset($data[static::DATA_REGENERATED])) {
                return ($entity->getAccessTime() >= new DateTime('-' . $this->regenerationForwardTime . ' MINUTES')) ? $this->startWith($data[static::DATA_REGENERATED]) : false;
            }

            $this->loadEntity($entity);

            return true;
        }

        return false;
    }
}
