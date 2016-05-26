<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Stream;

interface StreamInterface {

    /**
     * Return all data
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Close stream
     *
     * @return self
     */
    public function close();

    /**
     * Detach resource from Stream, return resource, if any
     *
     * @return resource|null
     */
    public function detach();

    /**
     * Is at the end of the stream
     *
     * @return boolean
     */
    public function eof(): boolean;

    /**
     * Remaining content
     *
     * @return string
     */
    public function getContents(): string;

    /**
     * Stream metadata
     *
     * @param string $key
     * @return mixed
     */
    public function getMetadata(string $key = null);

    /**
     * Get the size of the stream
     *
     * @return int|null
     */
    public function getSize();

    /**
     * Is the stream readable
     *
     * @return boolean
     */
    public function isReadable(): bool;

    /**
     * Is the stream seekable
     *
     * @return boolean
     */
    public function isSeekable(): bool;

    /**
     * Is the stream writable
     *
     * @return boolean
     */
    public function isWritable(): bool;

    /**
     * Read data
     *
     * @param int $length
     * @return string
     */
    public function read(int $length): string;

    /**
     * Seek to beginning
     *
     * @return self
     */
    public function rewind();

    /**
     * Seek to a position
     *
     * @param int $offset
     * @param int $whence
     * @return self
     */
    public function seek(int $offset, int $whence = SEEK_SET);

    /**
     * Return current position
     *
     * @return int
     */
    public function tell(): int;

    /**
     * Write data
     *
     * @param string $string
     * @return int
     */
    public function write(string $string): int;
}