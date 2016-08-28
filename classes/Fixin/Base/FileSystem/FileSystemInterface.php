<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileSystemInterface extends ResourceInterface {

    /**
     * Delete file
     *
     * @param string $filename
     * @return bool
     */
    public function delete(string $filename): bool;

    /**
     * Check file existence
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get extension of a filename
     *
     * @param string $path
     * @return string
     */
    public function extension(string $path): string;

    /**
     * Get the contents of a file
     *
     * @param string $filename
     * @return string
     */
    public function get(string $filename): string;

    /**
     * Get the contents of a file with lock
     *
     * @param string $filename
     * @return string
     */
    public function getWithLock(string $filename): string;

    /**
     * Determine if path is a file
     *
     * @param string $path
     * @return bool
     */
    public function isDirectory(string $path): bool;

    /**
     * Determine if path is a file
     *
     * @param string $path
     * @return bool
     */
    public function isFile(string $path): bool;

    /**
     * Determine if path is a file and is readable
     *
     * @param string $filename
     * @return bool
     */
    public function isReadable(string $filename): bool;

    /**
     * Put the contents of a file
     *
     * @param string $filename
     * @param string $contents
     * @return self
     */
    public function put(string $filename, string $contents): int;

    /**
     * Put the contents of a file with lock
     *
     * @param string $filename
     * @param string $contents
     * @return self
     */
    public function putWithLock(string $filename, string $contents): int;

    /**
     * Get real path
     *
     * @param string $path
     * @return string
     */
    public function realpath(string $path): string;

    /**
     * Get file size
     *
     * @param string $filename
     * @return int|false
     */
    public function size(string $filename);
}