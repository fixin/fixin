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
     * @see \Fixin\Base\FileSystem\FileSystemInterface::extension($path)
     */
    public function extension(string $path): string {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::get($filename, $lock)
     */
    public function get(string $filename, bool $lock = false): string {
        if ($this->isFile($filename)) {
            return $lock ? $this->lockedGet($filename) : file_get_contents($filename);
        }

        throw new FileNotFoundException(sprintf(static::EXCEPTION_FILE_NOT_EXISTS, $filename));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::isDirectory($path)
     */
    public function isDirectory(string $path): bool {
        return is_dir($path);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::isFile($path)
     */
    public function isFile(string $path): bool {
        return is_file($path);
    }

    /**
     * Get contents with lock
     *
     * @param string $filename
     * @return string
     */
    protected function lockedGet(string $filename): string {
        $contents = '';

        if ($handle = fopen($filename, 'r')) {
            if (flock($handle, LOCK_SH)) {
                while (!feof($handle)) {
                    $contents .= fread($handle, 1048576);
                }
            }

            fclose($handle);
        }

        return $contents;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::put($filename, $contents, $lock)
     */
    public function put(string $filename, string $contents, bool $lock = false): int {
        return file_put_contents($filename, $contents, $lock * LOCK_EX);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\FileSystem\FileSystemInterface::size($filename)
     */
    public function size(string $filename) {
        return filesize($filename);
    }
}