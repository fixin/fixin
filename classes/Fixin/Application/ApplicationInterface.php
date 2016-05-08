<?php

namespace Fixin\Application;

interface ApplicationInterface {

    /**
     * Gets the resource manager
     *
     * @return \Fixin\ResourceManager\ServiceLocatorInterface
     */
    public function getResourceManager();

    /**
     * Runs the application
     *
     * @return self
     */
    public function run();
}