<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Resource\Resource;
use Fixin\Support\Strings;

class FileResolver extends Resource implements FileResolverInterface {

    const EXCEPTION_FILE_SYSTEM_NOT_SET = 'File system not set';

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
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (!isset($this->fileSystem)) {
            throw new RuntimeException(static::EXCEPTION_FILE_SYSTEM_NOT_SET);
        }

        return $this;
    }

    /**
     * Get FileSystem instance
     *
     * @return FileSystemInterface
     */
    protected function getFileSystem(): FileSystemInterface {
        return $this->fileSystem ?: $this->loadLazyProperty('fileSystem');
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
     * Set file system
     *
     * @param string|FileSystemInterface $fileSystem
     */
    protected function setFileSystem($fileSystem) {
        $this->setLazyLoadingProperty('fileSystem', FileSystemInterface::class, $fileSystem);
    }

    /**
     * Set paths in normalized form
     *
     * @param array $paths
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setPaths(array $paths) {
        $this->paths = [];

        foreach ($paths as $path) {
            $this->paths[] = Strings::normalizePath($path);
        }
    }
}