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
            self::OPTION_FILE_SYSTEM => self::TYPE_INSTANCE
        ],
        THIS_SETS_LAZY = [
            self::OPTION_FILE_SYSTEM => FileSystemInterface::class
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
        return $this->fileSystem ?: $this->loadLazyProperty(static::OPTION_FILE_SYSTEM);
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

            if ($fileSystem->isReadable($fullName)) {
                return $fileSystem->getRealPath($fullName);
            }
        }

        return null;
    }

    protected function setDefaultExtension(string $defaultExtension): void
    {
        $this->defaultExtension = $defaultExtension;
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
