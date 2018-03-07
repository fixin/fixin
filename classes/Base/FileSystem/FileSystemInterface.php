<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileSystemInterface extends ResourceInterface
{
    /**
     * Delete file
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * Get the contents of a file
     *
     * @param string $filename
     * @return string
     */
    public function getFileContents(string $filename): string;

    /**
     * Get the contents of a file with lock
     *
     * @param string $filename
     * @return string
     */
    public function getFileContentsWithLock(string $filename): string;

    /**
     * Get file size
     *
     * @param string $filename
     * @return int|null
     */
    public function getFileSize(string $filename): ?int;

    /**
     * Get real path
     *
     * @param string $path
     * @return null|string
     */
    public function getRealPath(string $path): ?string;

    /**
     * Determine if directory exists
     *
     * @param string $path
     * @return bool
     */
    public function hasDirectory(string $path): bool;

    /**
     * Determine if path exists
     *
     * @param string $path
     * @return bool
     */
    public function hasEntry(string $path): bool;

    /**
     * Determine if file exists
     *
     * @param string $filename
     * @return bool
     */
    public function hasFile(string $filename): bool;

    /**
     * Determine if file exists and is readable
     *
     * @param string $filename
     * @return bool
     */
    public function hasReadableFile(string $filename): bool;

    /**
     * Include files recursive
     *
     * @param string $path
     * @param string $extension
     * @return $this
     */
    public function includeFilesRecursive(string $path, string $extension): FileSystemInterface;

    /**
     * Put the contents of a file
     *
     * @param string $filename
     * @param string $contents
     * @return int|null
     */
    public function putFileContents(string $filename, string $contents): ?int;

    /**
     * Put the contents of a file with lock
     *
     * @param string $filename
     * @param string $contents
     * @return int|null
     */
    public function putFileContentsWithLock(string $filename, string $contents): ?int;
}
