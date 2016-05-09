<?php

namespace Fixin\Application;

interface ApplicationInterface {

    const CONFIG_KEY = 'config';

    /**
     * Runs the application
     *
     * @return self
     */
    public function run();
}