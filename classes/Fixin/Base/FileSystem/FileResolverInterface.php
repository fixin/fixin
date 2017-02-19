<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileResolverInterface extends ResourceInterface
{
    public const
        OPTION_DEFAULT_EXTENSION = 'defaultExtension',
        OPTION_FILE_SYSTEM = 'fileSystem',
        OPTION_PATHS = 'paths';

    /**
     */
    public function resolve(string $filename): ?string;
}
