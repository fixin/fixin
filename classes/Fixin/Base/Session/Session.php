<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Exception\RuntimeException;
use Fixin\Resource\Prototype;
use Fixin\Resource\Resource;

class Session extends Prototype implements SessionInterface {

    const EXCEPTION_SESSION_MANAGER_NOT_SET = 'Session manager not set';

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (!isset($this->sessionManager)) {
            throw new RuntimeException(static::EXCEPTION_SESSION_MANAGER_NOT_SET);
        }

        return $this;
    }

    /**
     * Set session manager instance
     *
     * @param SessionManagerInterface $sessionManager
     */
    protected function setSessionManager(SessionManagerInterface $sessionManager) {
        $this->sessionManager = $sessionManager;
    }
}