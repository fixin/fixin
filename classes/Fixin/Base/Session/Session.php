<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Resource\Prototype;

class Session extends Prototype implements SessionInterface {

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * Set session manager instance
     *
     * @param SessionManagerInterface $sessionManager
     */
    protected function setSessionManager(SessionManagerInterface $sessionManager) {
        $this->sessionManager = $sessionManager;
    }
}