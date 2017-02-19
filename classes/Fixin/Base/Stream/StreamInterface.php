<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Stream;

interface StreamInterface
{
    /**
     * Return all data
     */
    public function __toString(): string;

    /**
     * Get remaining contents
     */
    public function getContents(): string;

    public function getCurrentPosition(): int;
    public function getMetadata(string $key = null);
    public function getSize(): ?int;
    public function isEof(): bool;
    public function isReadable(): bool;
    public function isSeekable(): bool;
    public function isWritable(): bool;

    /**
     * Read data
     */
    public function read(int $length): string;

    /**
     * Seek to the beginning
     */
    public function rewind(): StreamInterface;

    /**
     * Seek to a position
     */
    public function seek(int $offset, int $whence = SEEK_SET): StreamInterface;

    /**
     * Write data
     */
    public function write(string $string): int;
}
