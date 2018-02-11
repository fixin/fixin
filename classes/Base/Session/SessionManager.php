<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Session;

use DateTimeImmutable;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Model\Repository\RepositoryInterface;
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
            self::LIFETIME => Types::INT,
            self::REGENERATION_FORWARD_TIME => Types::INT,
            self::REPOSITORY => [self::LAZY_LOADING => RepositoryInterface::class]
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
     * @var SessionEntityInterface|null
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
     * @inheritDoc
     */
    public function clear(): SessionManagerInterface
    {
        $this->started || $this->start();

        $this->areas = [];
        $this->entity->setData($this->areas);

        $this->modified = true;

        return $this;
    }

    public function deleteGarbageSessions(int $lifetime): int
    {
        $request = $this->getRepository()->createRequest();
        $request->getWhere()->compare(SessionEntityInterface::ACCESS_TIME, '<', new DateTimeImmutable("+$lifetime MINUTES"));

        return $request->delete();
    }

    protected function generateId(): string
    {
        return sha1(Strings::generateRandomAlnum(24) . uniqid('', true) . microtime(true));
    }

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

    protected function getRepository(): RepositoryInterface
    {
        return $this->repository ?: $this->loadLazyProperty(static::REPOSITORY);
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

    protected function loadEntity(SessionEntityInterface $entity): void
    {
        $this->entity = $entity;
        $this->areas = $entity->getData();
        $this->sessionId = $entity->getSessionId();

        if ($this->lifetime) {
            $this->setupCookie();
        }

        $request = $this->getRepository()->createRequest();
        $request->getWhere()->compare(SessionEntityInterface::SESSION_ID, '=', $this->sessionId);
        $request->update([SessionEntityInterface::ACCESS_TIME => new DateTimeImmutable()]);
    }

    /**
     * @inheritDoc
     */
    public function regenerateId(): SessionManagerInterface
    {
        $this->started || $this->start();

        $this->sessionId = $this->generateId();
        $this->modified = true;

        if ($this->entity->isStored()) {
            $this->entity
                ->setData([static::DATA_REGENERATED => $this->sessionId])
                ->setAccessTime(new DateTimeImmutable())
                ->save();

            // New entity
            $this->entity = $this->getRepository()->create();
        }

        $this->setupCookie();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(): SessionManagerInterface
    {
        if ($this->started && $this->isModified()) {
            $this->entity
                ->setSessionId($this->sessionId)
                ->setData($this->areas)
                ->setAccessTime(new DateTimeImmutable())
                ->save();

            $this->modified = false;

            foreach ($this->areas as $area) {
                $area->setModified(false);
            }
        }

        return $this;
    }

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

        $sessionId = $this->getCookieManager()->get($this->cookieName);
        if ($sessionId && $this->startWithId($sessionId)) {
            return $this;
        }

        // New session
        $this->areas = [];
        $this->entity = $this->getRepository()->create();

        $this->regenerateId();

        return $this;
    }

    /**
     * Start with stored session id
     */
    protected function startWithId(string $sessionId): bool
    {
        $request = $this->getRepository()->createRequest();
        $where = $request->getWhere()->compare(SessionEntityInterface::SESSION_ID, '=', $sessionId);

        if ($this->lifetime) {
            $where->compare(SessionEntityInterface::ACCESS_TIME, '>=', new DateTimeImmutable('+' . $this->lifetime . ' MINUTES'));
        }

        /** @var SessionEntityInterface $entity */
        if ($entity = $request->fetchFirst()) {
            $data = $entity->getData();
            if (isset($data[static::DATA_REGENERATED])) {
                return ($entity->getAccessTime() >= new DateTimeImmutable('-' . $this->regenerationForwardTime . ' MINUTES')) ? $this->startWith($data[static::DATA_REGENERATED]) : false;
            }

            $this->loadEntity($entity);

            return true;
        }

        return false;
    }
}
