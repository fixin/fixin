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
     * Display error page
     *
     * @return $this
     */
    public function displayErrorPage(int $statusCode): ApplicationInterface;

    /**
     * Run the application
     *
     * @return $this
     */
    public function run(): ApplicationInterface;
}
