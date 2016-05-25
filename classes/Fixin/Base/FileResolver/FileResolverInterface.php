<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileResolver;

interface FileResolverInterface {

    /**
     * Resolve filename to fullpath
     * @param string $filename
     * @return string|null
     */
    public function resolve(string $filename);
}