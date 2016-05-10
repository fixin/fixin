<?php

namespace Fixin\Base\Autoloader;

interface AutoloaderInterface {

    /**
     * Autoload callback
     *
     * @param string $class
     */
    public function autoload(string $class);

    /**
     * Register to autoloader stack
     *
     * @return self
     */
    public function register();
}