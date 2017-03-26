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
    public function autoloadCallback(string $class): void;
}

/**
 * Encapsulated include
 */
function fixinBaseAutoloaderEncapsulatedInclude(string $filename): void
{
    include $filename;
}
