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
     */
    public function delete(string $filename): bool;

    /**
     * Get the contents of a file
     */
    public function getFileContents(string $filename): string;

    /**
     * Get the contents of a file with lock
     */
    public function getFileContentsWithLock(string $filename): string;

    public function getFileSize(string $filename): ?int;
    public function getRealPath(string $path): ?string;
    public function includeFilesRecursive(string $path, string $extension): FileSystemInterface;
    public function hasDirectory(string $path): bool;

    /**
     * Determine if file system has entry at path
     */
    public function hasEntry(string $path): bool;

    public function hasFile(string $path): bool;
    public function hasReadableFile(string $filename): bool;

    /**
     * Put the contents of a file
     */
    public function putFileContents(string $filename, string $contents): ?int;

    /**
     * Put the contents of a file with lock
     */
    public function putFileContentsWithLock(string $filename, string $contents): ?int;
}
