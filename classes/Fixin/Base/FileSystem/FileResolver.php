<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\Resource;
use Fixin\Support\Strings;

class FileResolver extends Resource implements FileResolverInterface
{
    protected const
        THIS_REQUIRES = [
            self::FILE_SYSTEM
        ],
        THIS_SETS = [
            self::DEFAULT_EXTENSION => self::STRING_TYPE
        ],
        THIS_SETS_LAZY = [
            self::FILE_SYSTEM => FileSystemInterface::class
        ];

    /**
     * @var string
     */
    protected $defaultExtension = '';

    /**
     * @var FileSystemInterface|false|null
     */
    protected $fileSystem;

    /**
     * @var string[]
     */
    protected $paths = [];

    protected function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem ?: $this->loadLazyProperty(static::FILE_SYSTEM);
    }

    public function resolve(string $filename): ?string
    {
        $fileSystem = $this->getFileSystem();

        // Default extension
        if (Strings::extractExtension($filename) === '') {
            $filename .= $this->defaultExtension;
        }

        // Search
        foreach ($this->paths as $path) {
            $fullName = $path . $filename;

            if ($fileSystem->hasReadableFile($fullName)) {
                return $fileSystem->getRealPath($fullName);
            }
        }

        return null;
    }

    /**
     * Set paths in normalized form
     */
    protected function setPaths(array $paths): void
    {
        $this->paths = [];

        foreach ($paths as $path) {
            $this->paths[] = Strings::normalizePath($path);
        }
    }
}
