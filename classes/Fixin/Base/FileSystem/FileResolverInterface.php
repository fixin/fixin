<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileResolverInterface extends ResourceInterface {

    /**
     * Resolve filename to fullpath
     * @param string $filename
     * @return string|null
     */
    public function resolve(string $filename);
}