<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Application;

interface ApplicationInterface
{
    /**
     * @return $this
     */
    public function run(): ApplicationInterface;
}
