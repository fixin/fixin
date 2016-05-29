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

    /**
     * @var string
     */
    protected $defaultExtension;

    /**
     * @var string[]
     */
    protected $paths = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileResolver\FileResolverInterface::resolve()
     */
    public function resolve(string $filename) {
        // Default extension
        if (pathinfo($filename, PATHINFO_EXTENSION) === '') {
            $filename .= $this->defaultExtension;
        }

        // Search
        foreach ($this->paths as $path) {
            $fullname = $path . $filename;

            if (is_readable($fullname)) {
                return realpath($fullname);
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