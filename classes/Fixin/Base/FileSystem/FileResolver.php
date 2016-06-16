<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\Resource;
use Fixin\Support\Strings;

class FileResolver extends Resource implements FileResolverInterface {

    const THIS_REQUIRES = [
        self::OPTION_FILE_SYSTEM => self::TYPE_INSTANCE
    ];
    const THIS_SETS_LAZY = [
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

    /**
     * Get FileSystem instance
     *
     * @return FileSystemInterface
     */
    protected function getFileSystem(): FileSystemInterface {
        return $this->fileSystem ?: $this->loadLazyProperty(static::OPTION_FILE_SYSTEM);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileResolverInterface::resolve($filename)
     */
    public function resolve(string $filename):string {
        $fileSystem = $this->getFileSystem();

        // Default extension
        if ($fileSystem->extension($filename) === '') {
            $filename .= $this->defaultExtension;
        }

        // Search
        foreach ($this->paths as $path) {
            $fullname = $path . $filename;

            if ($fileSystem->isReadable($fullname)) {
                return $fileSystem->realpath($fullname);
            }
        }

        return '';
    }

    /**
     * Set default extension for filenames
     *
     * @param string $defaultExtension
     */
    protected function setDefaultExtension(string $defaultExtension) {
        $this->defaultExtension = $defaultExtension;
    }

    /**
     * Set paths in normalized form
     *
     * @param array $paths
     */
    protected function setPaths(array $paths) {
        $this->paths = [];

        foreach ($paths as $path) {
            $this->paths[] = Strings::normalizePath($path);
        }
    }
}