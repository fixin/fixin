<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Autoloader;

interface AutoloaderInterface
{
    /**
     * Autoload callback
     *
     * @param string $class
     */
    public function autoloadCallback(string $class): void;
}

/**
 * Encapsulated include
 *
 * @param string $filename
 */
function fixinBaseAutoloaderEncapsulatedInclude(string $filename): void
{
    include $filename;
}
