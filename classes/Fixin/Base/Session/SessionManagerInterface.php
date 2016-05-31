<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Resource\ResourceInterface;

interface SessionManagerInterface extends ResourceInterface {

    const OPTION_REPOSITORY = 'repository';

    /**
     * Get session for name
     *
     * @param string $name
     * @return SessionInterface
     */
    public function getSession(string $name): SessionInterface;
}