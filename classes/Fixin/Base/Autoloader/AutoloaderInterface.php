<?php
/**
 * Fixin Framework
 *
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
}

/**
 * Encapsulated include
 *
 * @param string $filename
 */
function fixinBaseAutoloaderEncapsulatedInclude(string $filename) {
    include $filename;
}