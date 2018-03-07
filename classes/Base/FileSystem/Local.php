<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\Resource;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Local extends Resource implements FileSystemInterface
{
    protected const
        FILE_NOT_FOUND_EXCEPTION = "File not found at '%s'",
        FILE_READ_FAILURE_EXCEPTION = "File read failure '%s'",
        FILE_WRITE_FAILURE_EXCEPTION = "File write failure '%s'";

    /**
     * @inheritDoc
     */
    public function delete(string $path): bool
    {
        return unlink($path);
    }

    /**
     * @inheritDoc
     */
    public function getFileContents(string $filename): string
    {
        if ($this->hasFile($filename)) {
            $content = file_get_contents($filename);

            if ($content !== false) {
                return $content;
            }

            throw new Exception\FileReadFailureException(sprintf(static::FILE_READ_FAILURE_EXCEPTION, $filename));
        }

        throw new Exception\FileNotFoundException(sprintf(static::FILE_NOT_FOUND_EXCEPTION, $filename));
    }

    /**
     * @inheritDoc
     */
    public function getFileContentsWithLock(string $filename): string
    {
        if ($this->hasFile($filename)) {
            $content = $this->getSharedFileContents($filename);

            if ($content !== null) {
                return $content;
            }

            throw new Exception\FileReadFailureException(sprintf(static::FILE_READ_FAILURE_EXCEPTION, $filename));
        }

        throw new Exception\FileNotFoundException(sprintf(static::FILE_NOT_FOUND_EXCEPTION, $filename));
    }

    /**
     * @inheritDoc
     */
    public function getFileSize(string $filename): ?int
    {
        return ($size = filesize($filename)) !== false ? $size : null;
    }

    /**
     * @inheritDoc
     */
    public function getRealPath(string $path): ?string
    {
        return ($resolved = realpath($path)) !== false ? $resolved : null;
    }

    /**
     * Get shared file contents
     *
     * @param string $filename
     * @return null|string
     */
    protected function getSharedFileContents(string $filename): ?string
    {
        $contents = null;

        if ($handle = fopen($filename, 'r')) {
            if (flock($handle, LOCK_SH)) {
                $contents = '';

                while (!feof($handle)) {
                    $contents .= fread($handle, 1048576);
                }
            }

            fclose($handle);
        }

        return $contents;
    }

    /**
     * @inheritDoc
     */
    public function hasDirectory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * @inheritDoc
     */
    public function hasEntry(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @inheritDoc
     */
    public function hasFile(string $filename): bool
    {
        return is_file($filename);
    }

    /**
     * @inheritDoc
     */
    public function hasReadableFile(string $filename): bool
    {
        return is_readable($filename);
    }

    /**
     * @inheritDoc
     */
    public function includeFilesRecursive(string $path, string $extension): FileSystemInterface
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($iterator as $item) {
            if ($item->isFile() && strtolower($item->getExtension()) === $extension) {
                include_once $item;
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function putFileContents(string $filename, string $contents): int
    {
        return $this->putFileContentsProcess($filename, $contents, 0);
    }

    /**
     * @inheritDoc
     */
    public function putFileContentsWithLock(string $filename, string $contents): int
    {
        return $this->putFileContentsProcess($filename, $contents, LOCK_EX);
    }

    /**
     * Put file contents process
     *
     * @param string $filename
     * @param string $contents
     * @param int $flags
     * @return int
     */
    protected function putFileContentsProcess(string $filename, string $contents, int $flags): int
    {
        $written = file_put_contents($filename, $contents, $flags);

        if ($written !== false) {
            return $written;
        }

        throw new Exception\FileWriteFailureException(sprintf(static::FILE_WRITE_FAILURE_EXCEPTION, $filename));
    }
}
