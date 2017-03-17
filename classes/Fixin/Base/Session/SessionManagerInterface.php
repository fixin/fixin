<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Resource\PrototypeInterface;

interface SessionManagerInterface extends PrototypeInterface
{
    public const
        OPTION_COOKIE_MANAGER = 'cookieManager',
        OPTION_COOKIE_NAME = 'cookieName',
        OPTION_LIFETIME = 'lifetime',
        OPTION_REGENERATION_FORWARD_TIME = 'regenerationForwardTime',
        OPTION_REPOSITORY = 'repository';

    /**
     * Clear data
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

    public function regenerateId(): SessionManagerInterface;
    public function save(): SessionManagerInterface;
    public function start(): SessionManagerInterface;
}
