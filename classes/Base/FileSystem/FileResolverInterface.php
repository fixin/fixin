<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileResolverInterface extends ResourceInterface
{
    public const
        DEFAULT_EXTENSION = 'defaultExtension',
        FILE_SYSTEM = 'fileSystem',
        PATHS = 'paths';

    public function resolve(string $filename): ?string;
}
