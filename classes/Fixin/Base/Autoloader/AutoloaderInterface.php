<?php
/**
 * @link       http://www.attilajenei.com
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

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