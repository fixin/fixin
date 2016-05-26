<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Stream;

interface StreamInterface extends PrototypeInterface {

    /**
     * Return all data
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Close the stream
     *
     * @return self
     */
    public function close();

    /**
     * Detach resources
     *
     * @return self
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
     * Return current position
     *
     * @return int
     */
    public function tell(): int;

    /**
     * Is the stream readable
     *
     * @return boolean
     */
    public function isReadable(): boolean;

    /**
     * Is the stream seekable
     *
     * @return boolean
     */
    public function isSeekable(): boolean;

    /**
     * Is the stream writable
     *
     * @return boolean
     */
    public function isWritable(): boolean;

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
     * Write data
     *
     * @param string $string
     * @return int
     */
    public function write(string $string): int;
}