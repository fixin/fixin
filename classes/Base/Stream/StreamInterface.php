<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Stream;

interface StreamInterface
{
    /**
     * Return all data
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Get current position
     *
     * @return int
     */
    public function getCurrentPosition(): int;

    /**
     * getMetadata
     *
     * @param string|null $key
     * @return mixed
     */
    public function getMetadata(string $key = null);

    /**
     * Get remaining contents
     *
     * @return string
     */
    public function getRemainingContents(): string;

    /**
     * Get the size of the stream
     *
     * @return int|null
     */
    public function getSize(): ?int;

    /**
     * Determine if at the end of the stream
     *
     * @return bool
     */
    public function isEof(): bool;

    /**
     * Determine if the stream readable
     *
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * Determine if the stream seekable
     *
     * @return bool
     */
    public function isSeekable(): bool;

    /**
     * Determine if the stream writable
     *
     * @return bool
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
     * Seek to the beginning
     *
     * @return $this
     */
    public function rewind(): StreamInterface;

    /**
     * Seek to a position
     *
     * @param int $position
     * @param int $whence
     * @return $this
     */
    public function seek(int $position, int $whence = SEEK_SET): StreamInterface;

    /**
     * Write data
     *
     * @param string $string
     * @return int
     */
    public function write(string $string): int;
}
