<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\Resource;
use Fixin\Base\Exception\InvalidArgumentException;
use Fixin\Base\Exception\RuntimeException;

class FileResolver extends Resource implements FileResolverInterface {

    const EXCEPTION_FILE_SYSTEM_NOT_SET = 'File system not set';
    const EXCEPTION_INVALID_FILE_SYSTEM_ARGUMENT = "Invalid 'fileSystem' argument: string or FileSystemInterface allowed";

    /**
     * @var string
     */
    protected $defaultExtension = '';

    /**
     * @var FileSystemInterface
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
    protected function configurationTests() {
        if (!isset($this->fileSystem)) {
            throw new RuntimeException(static::EXCEPTION_FILE_SYSTEM_NOT_SET);
        }
    }

    /**
     * Get FileSystem instance
     *
     * @return FileSystemInterface
     */
    protected function getFileSystem(): FileSystemInterface {
        return is_object($fileSystem = $this->fileSystem) ? $fileSystem : ($this->fileSystem = $this->container->get($fileSystem));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileResolverInterface::resolve()
     */
    public function resolve(string $filename) {
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

        return null;
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
     * @throws InvalidArgumentException
     */
    protected function setFileSystem($fileSystem) {
        if (is_string($fileSystem) || $fileSystem instanceof FileSystemInterface) {
            $this->fileSystem = $fileSystem;

            return;
        }

        throw new InvalidArgumentException(sprintf(InvalidArgumentException::MESSAGE, 'fileSystem', 'string or FileSystemInterface'));
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