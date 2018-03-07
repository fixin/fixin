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
        KEY_PREFIX = 'keyPrefix',
        LIFETIME = 'lifetime',
        REGENERATION_FORWARD_TIME = 'regenerationForwardTime',
        STORE = 'store';

    /**
     * Clear data
     *
     * @return $this
     */
    public function clear(): SessionManagerInterface;

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
     * @return $this
     */
    public function regenerateId(): SessionManagerInterface;

    /**
     * Save data
     *
     * @return $this
     */
    public function save(): SessionManagerInterface;

    /**
     * Start session
     *
     * @return $this
     */
    public function start(): SessionManagerInterface;
}
