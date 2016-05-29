<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Base\FileSystem\Exception\FileNotFoundException;

class Local extends FileSystem {

    const EXCEPTION_FILE_NOT_EXISTS = "File not exists at '%s'";

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::delete($filename)
     */
    public function delete(string $filename): bool {
        return unlink($filename);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::exists($filename)
     */
    public function exists(string $path): bool {
        return file_exists($path);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::get($filename)
     */
    public function get(string $filename): string {
        if ($this->isFile($filename)) {
            return file_get_contents($filename);
        }

        throw new FileNotFoundException(sprintf(static::EXCEPTION_FILE_NOT_EXISTS, $filename));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::isDirectory($filename)
     */
    public function isDirectory(string $path): bool {
        return is_dir($path);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::isFile($filename)
     */
    public function isFile(string $path): bool {
        return is_file($path);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::size($filename)
     */
    public function size(string $filename) {
        return filesize($filename);
    }
}