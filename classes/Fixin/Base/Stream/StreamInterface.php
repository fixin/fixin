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
     */
    public function __toString(): string;

    public function getRemainingContents(): string;
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
     *
     * @return $this
     */
    public function rewind(): StreamInterface;

    /**
     * Seek to a position
     *
     * @return $this
     */
    public function seek(int $position, int $whence = SEEK_SET): StreamInterface;

    /**
     * Write data
     */
    public function write(string $string): int;
}
