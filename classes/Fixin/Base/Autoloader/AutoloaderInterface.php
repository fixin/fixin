<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Autoloader;

interface AutoloaderInterface
{
    /**
     * Autoload callback
     */
    public function autoload(string $class): void;
}

/**
 * Encapsulated include
 */
function fixinBaseAutoloaderEncapsulatedInclude(string $filename): void {
    include $filename;
}
