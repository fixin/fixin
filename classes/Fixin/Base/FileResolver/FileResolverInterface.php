<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileResolver;

use Fixin\Resource\ResourceInterface;

interface FileResolverInterface extends ResourceInterface {

    /**
     * Resolve filename to fullpath
     * @param string $filename
     * @return string|null
     */
    public function resolve(string $filename);
}