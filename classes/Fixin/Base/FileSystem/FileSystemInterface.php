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
    public function delete(string $path): bool;
    public function getFileContents(string $filename): string;
    public function getFileContentsWithLock(string $filename): string;
    public function getFileSize(string $filename): ?int;
    public function getRealPath(string $path): ?string;

    /**
     * @return $this
     */
    public function includeFilesRecursive(string $path, string $extension): FileSystemInterface;

    public function hasDirectory(string $path): bool;
    public function hasEntry(string $path): bool;
    public function hasFile(string $filename): bool;
    public function hasReadableFile(string $filename): bool;
    public function putFileContents(string $filename, string $contents): ?int;
    public function putFileContentsWithLock(string $filename, string $contents): ?int;
}
