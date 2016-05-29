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
     * @param string $filename
     * @return bool
     */
    public function delete(string $filename): bool;

    /**
     * Check the file exists
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get the contents of a file
     *
     * @param string $filename
     * @return string
     */
    public function get(string $filename): string;

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
     * Put the contents of a file
     *
     * @param string $filename
     * @param string $content
     * @return self
     */
    public function put(string $filename, string $content);

    /**
     * Get file size
     *
     * @param string $filename
     * @return int|false
     */
    public function size(string $filename);
}