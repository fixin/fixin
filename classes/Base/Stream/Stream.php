<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Stream;

class Stream implements StreamInterface
{
    protected const
        INVALID_STREAM_EXCEPTION = 'Invalid stream',
        INVALID_STREAM_REFERENCE_EXCEPTION = 'Invalid stream reference',
        NOT_READABLE_EXCEPTION = 'Stream is not readable',
        NOT_SEEKABLE_EXCEPTION = 'Stream is not seekable',
        NOT_WRITABLE_EXCEPTION = 'Stream is not writable',
        POSITION_FAILURE_EXCEPTION = 'Unable to determine position',
        READ_FAILURE_EXCEPTION = 'Stream read error',
        SEEK_FAILURE_EXCEPTION = 'Stream seek error',
        WRITE_FAILURE_EXCEPTION = 'Stream write error';

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @param string|resource $stream
     * @throws Exception\InvalidStreamException
     */
    public function __construct($stream, string $mode = 'r')
    {
        // By reference
        if (is_string($stream)) {
            $stream = $this->resourceByReference($stream, $mode);
        }

        // Stream
        if (is_resource($stream) && get_resource_type($stream) === 'stream') {
            $this->resource = $stream;

            return;
        }

        throw new Exception\InvalidStreamException(static::INVALID_STREAM_EXCEPTION);
    }

    public function __destruct()
    {
        fclose($this->resource);
    }

    public function __toString(): string
    {
        $this->rewind();

        return $this->getRemainingContents();
    }

    /**
     * @throws Exception\PositionFailureException
     */
    public function getCurrentPosition(): int
    {
        $result = ftell($this->resource);

        if (is_int($result)) {
            return $result;
        }

        throw new Exception\PositionFailureException(static::POSITION_FAILURE_EXCEPTION);
    }

    /**
     * @throws Exception\NotReadableException
     * @throws Exception\ReadFailureException
     */
    public function getRemainingContents(): string
    {
        if (!$this->isReadable()) {
            throw new Exception\NotReadableException(static::NOT_READABLE_EXCEPTION);
        }

        if (false !== $result = stream_get_contents($this->resource)) {
            return $result;
        }

        throw new Exception\ReadFailureException(static::READ_FAILURE_EXCEPTION);
    }

    public function getMetadata(string $key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (isset($key)) {
            return $metadata[$key] ?? null;
        }

        return $metadata;
    }

    public function getSize(): ?int
    {
        return fstat($this->resource)['size'] ?? null;
    }

    public function isEof(): bool
    {
        return feof($this->resource);
    }

    public function isReadable(): bool
    {
        $mode = stream_get_meta_data($this->resource)['mode'];

        return strcspn($mode, 'r+') < strlen($mode);
    }

    public function isSeekable(): bool
    {
        return stream_get_meta_data($this->resource)['seekable'];
    }

    public function isWritable(): bool
    {
        $mode = stream_get_meta_data($this->resource)['mode'];

        return strcspn($mode, 'xwca+') < strlen($mode);
    }

    /**
     * @throws Exception\NotReadableException
     * @throws Exception\ReadFailureException
     */
    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw new Exception\NotReadableException(static::NOT_READABLE_EXCEPTION);
        }

        if (false !== $result = fread($this->resource, $length)) {
            return $result;
        }

        throw new Exception\ReadFailureException(static::READ_FAILURE_EXCEPTION);
    }

    /**
     * Open resource
     *
     * @throws Exception\InvalidStreamException
     */
    protected function resourceByReference(string $reference, string $mode)
    {
        $error = null;

        // Suppress warnings
        set_error_handler(function($e) use (&$error) {
            $error = $e;
        });

        // Open
        $stream = fopen($reference, $mode);

        restore_error_handler();

        if ($error) {
            throw new Exception\InvalidStreamException(static::INVALID_STREAM_REFERENCE_EXCEPTION);
        }

        return $stream;
    }

    /**
     * @return $this
     */
    public function rewind(): StreamInterface
    {
        return $this->seek(0);
    }

    /**
     * @throws Exception\NotSeekableException
     * @throws Exception\SeekFailureException
     * @return $this
     */
    public function seek(int $position, int $whence = SEEK_SET): StreamInterface
    {
        if (!$this->isSeekable()) {
            throw new Exception\NotSeekableException(static::NOT_SEEKABLE_EXCEPTION);
        }

        if (fseek($this->resource, $position, $whence) === 0) {
            return $this;
        }

        throw new Exception\SeekFailureException(static::SEEK_FAILURE_EXCEPTION);
    }

    /**
     * @throws Exception\NotWritableException
     * @throws Exception\WriteFailureException
     */
    public function write(string $string): int
    {
        if (!$this->isWritable()) {
            throw new Exception\NotWritableException(static::NOT_WRITABLE_EXCEPTION);
        }

        if (false !== $result = fwrite($this->resource, $string)) {
            return $result;
        }

        throw new Exception\WriteFailureException(static::WRITE_FAILURE_EXCEPTION);
    }
}
