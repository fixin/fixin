<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Resource\PrototypeInterface;

interface SessionManagerInterface extends PrototypeInterface {

    const
    OPTION_COOKIE_MANAGER = 'cookieManager',
    OPTION_COOKIE_NAME = 'cookieName',
    OPTION_LIFETIME = 'lifetime',
    OPTION_REGENERATION_FORWARD_TIME = 'regenerationForwardTime',
    OPTION_REPOSITORY = 'repository';

    /**
     * Clear data
     *
     * @return SessionManagerInterface
     */
    public function clear(): SessionManagerInterface;

    /**
     * Garbage collection
     *
     * @param int $lifetime
     * @return int
     */
    public function garbageCollection(int $lifetime): int;

    /**
     * Get session area for name
     *
     * @param string $name
     * @return SessionAreaInterface
     */
    public function getArea(string $name): SessionAreaInterface;

    /**
     * Get cookie name
     *
     * @return string
     */
    public function getCookieName(): string;

    /**
     * Get lifetime
     *
     * @return int
     */
    public function getLifetime(): int;

    /**
     * Get regeneration forward time
     *
     * @return int
     */
    public function getRegenerationForwardTime(): int;

    /**
     * Determine if at least one area modified
     *
     * @return bool
     */
    public function isModified(): bool;

    /**
     * Regenerate session ID
     *
     * @return self
     */
    public function regenerateId(): SessionManagerInterface;

    /**
     * Save session
     *
     * @return self
     */
    public function save(): SessionManagerInterface;

    /**
     * Start session
     *
     * @return self
     */
    public function start(): SessionManagerInterface;
}