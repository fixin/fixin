<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Resource\PrototypeInterface;

interface SessionManagerInterface extends PrototypeInterface {

    const OPTION_COOKIE_MANAGER = 'cookieManager';
    const OPTION_COOKIE_NAME = 'cookieName';
    const OPTION_LIFETIME = 'lifetime';
    const OPTION_REPOSITORY = 'repository';

    /**
     * Get session area for name
     *
     * @param string $name
     * @return SessionAreaInterface
     */
    public function getArea(string $name): SessionAreaInterface;

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