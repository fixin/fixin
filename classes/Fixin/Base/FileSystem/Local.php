<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Base\FileSystem\Exception;
use Fixin\Resource\Resource;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Local extends Resource implements FileSystemInterface
{
    protected const
        EXCEPTION_FILE_NOT_EXISTS = "File not exists at '%s'",
        EXCEPTION_FILE_READ_FAILURE = "File read failure '%s'",
        EXCEPTION_FILE_WRITE_FAILURE = "File read failure '%s'";

    public function delete(string $filename): bool
    {
        return unlink($filename);
    }

    public function getExtension(string $path): ?string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @throws Exception\FileNotFoundException
     * @throws Exception\FileReadFailureException
     */
    public function getFileContents(string $filename): string
    {
        if ($this->isFile($filename)) {
            $content = file_get_contents($filename);

            if ($content !== false) {
                return $content;
            }

            throw new Exception\FileReadFailureException(sprintf(static::EXCEPTION_FILE_READ_FAILURE, $filename));
        }

        throw new Exception\FileNotFoundException(sprintf(static::EXCEPTION_FILE_NOT_EXISTS, $filename));
    }

    /**
     * @throws Exception\FileNotFoundException
     * @throws Exception\FileReadFailureException
     */
    public function getFileContentsWithLock(string $filename): string
    {
        if ($this->isFile($filename)) {
            $content = $this->getSharedFileContents($filename);

            if ($content !== null) {
                return $content;
            }

            throw new Exception\FileReadFailureException(sprintf(static::EXCEPTION_FILE_READ_FAILURE, $filename));
        }

        throw new Exception\FileNotFoundException(sprintf(static::EXCEPTION_FILE_NOT_EXISTS, $filename));
    }

    public function getFileSize(string $filename): ?int
    {
        return ($size = filesize($filename)) !== false ? $size : null;
    }

    public function getRealPath(string $path): ?string
    {
        return ($resolved = realpath($path)) !== false ? $resolved : null;
    }

    protected function getSharedFileContents(string $filename): ?string
    {
        $contents = null;

        if (($handle = fopen($filename, 'r')) && flock($handle, LOCK_SH)) {
            $contents = '';

            while (!feof($handle)) {
                $contents .= fread($handle, 1048576);
            }
        }

        fclose($handle);

        return $contents;
    }

    /**
     * @return static
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

    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public function isExisting(string $path): bool
    {
        return file_exists($path);
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function isReadable(string $filename): bool
    {
        return is_readable($filename);
    }

    public function putFileContents(string $filename, string $contents): int
    {
        return $this->putFileContentsProcess($filename, $contents, 0);
    }

    public function putFileContentsWithLock(string $filename, string $contents): int
    {
        return $this->putFileContentsProcess($filename, $contents, LOCK_EX);
    }

    /**
     * @throws Exception\FileWriteFailureException
     */
    protected function putFileContentsProcess(string $filename, string $contents, int $flags): int
    {
        $written = file_put_contents($filename, $contents, $flags);

        if ($written !== false) {
            return $written;
        }

        throw new Exception\FileWriteFailureException(sprintf(static::EXCEPTION_FILE_WRITE_FAILURE, $filename));
    }
}
