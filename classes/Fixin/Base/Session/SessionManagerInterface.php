<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Session;

use Fixin\Resource\PrototypeInterface;

interface SessionManagerInterface extends PrototypeInterface
{
    public const
        COOKIE_MANAGER = 'cookieManager',
        COOKIE_NAME = 'cookieName',
        LIFETIME = 'lifetime',
        REGENERATION_FORWARD_TIME = 'regenerationForwardTime',
        REPOSITORY = 'repository';

    /**
     * Clear data
     *
     * @return $this
     */
    public function clear(): SessionManagerInterface;

    public function deleteGarbageSessions(int $lifetime): int;
    public function getArea(string $name): SessionAreaInterface;
    public function getCookieName(): string;
    public function getLifetime(): int;
    public function getRegenerationForwardTime(): int;

    /**
     * Determine if at least one area modified
     */
    public function isModified(): bool;

    /**
     * @return $this
     */
    public function regenerateId(): SessionManagerInterface;

    /**
     * @return $this
     */
    public function save(): SessionManagerInterface;

    /**
     * @return $this
     */
    public function start(): SessionManagerInterface;
}
