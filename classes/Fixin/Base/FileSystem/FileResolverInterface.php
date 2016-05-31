<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileResolverInterface extends ResourceInterface {

    const OPTION_DEFAULT_EXTENSION = 'defaultExtension';
    const OPTION_FILE_SYSTEM = 'fileSystem';
    const OPTION_PATHS = 'paths';

    /**
     * Resolve filename to fullpath
     *
     * @param string $filename
     * @return string
     */
    public function resolve(string $filename): string;
}