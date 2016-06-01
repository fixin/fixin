<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Stream;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\Exception\InvalidArgumentException;

class Stream implements StreamInterface {

    const EXCEPTION_INVALID_STREAM = 'Invalid stream';
    const EXCEPTION_INVALID_STREAM_REFERENCE = 'Invalid stream reference';
    const EXCEPTION_NOT_READABLE = 'Stream is not readable';
    const EXCEPTION_NOT_SEEKABLE = 'Stream is not seekable';
    const EXCEPTION_READ_ERROR = 'Read error for stream';
    const EXCEPTION_SEEK_ERROR = 'Seek error for stream';
    const EXCEPTION_UNABLE_TO_DETERMINE_POSITION = 'Unable to determine position';

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @param string|resource $stream
     * @param string $mode
     * @throws InvalidArgumentException
     */
    public function __construct($stream, string $mode = 'r') {
        // By reference
        if (is_string($stream)) {
            $stream = $this->resourceByReference($stream, $mode);
        }

        // Stream
        if (is_resource($stream) && get_resource_type($stream) === 'stream') {
            $this->resource = $stream;

            return;
        }

        throw new InvalidArgumentException(static::EXCEPTION_INVALID_STREAM);
    }

    public function __destruct() {
        fclose($this->resource);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::__toString()
     */
    public function __toString(): string {
        if ($this->isReadable()) {
            try {
                $this->rewind();
                return $this->getContents();
            }
            catch (RuntimeException $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::eof()
     */
    public function eof(): bool {
        return feof($this->resource);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::getContents()
     */
    public function getContents(): string {
        if (!$this->isReadable()) {
            throw new RuntimeException(static::EXCEPTION_NOT_READABLE);
        }

        if (false !== $result = stream_get_contents($this->resource)) {
            return $result;
        }

        throw new RuntimeException(static::EXCEPTION_READ_ERROR);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::getMetadata($key)
     */
    public function getMetadata(string $key = null) {
        $metadata = stream_get_meta_data($this->resource);

        if (isset($key)) {
            return $metadata[$key] ?? null;
        }

        return $metadata;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::getSize()
     */
    public function getSize() {
        return fstat($this->resource)['size'];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::isReadable()
     */
    public function isReadable(): bool {
        $mode = $mode = stream_get_meta_data($this->resource)['mode'];

        return strcspn($mode, 'r+') < strlen($mode);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::isSeekable()
     */
    public function isSeekable(): bool {
        return stream_get_meta_data($this->resource)['seekable'];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::isWritable()
     */
    public function isWritable(): bool {
        $mode = $mode = stream_get_meta_data($this->resource)['mode'];

        return strcspn($mode, 'xwca+') < strlen($mode);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::read($length)
     */
    public function read(int $length): string {
        if (!$this->isReadable()) {
            throw new RuntimeException(static::EXCEPTION_NOT_READABLE);
        }

        if (false !== $result = fread($this->resource, $length)) {
            return $result;
        }

        throw new RuntimeException(static::EXCEPTION_READ_ERROR);
    }

    /**
     * Open resource
     *
     * @param string $reference
     * @param string $mode
     * @throws InvalidArgumentException
     * @return resource
     */
    protected function resourceByReference(string $reference, string $mode) {
        $error = null;

        // Suppress warnings
        set_error_handler(function($e) use (&$error) {
            $error = $e;
        });

        // Open
        $stream = fopen($reference, $mode);

        restore_error_handler();

        if ($error) {
            throw new InvalidArgumentException(static::EXCEPTION_INVALID_STREAM_REFERENCE);
        }

        return $stream;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::rewind()
     */
    public function rewind(): StreamInterface {
        return $this->seek(0);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::seek($offset, $whence)
     */
    public function seek(int $offset, int $whence = SEEK_SET): StreamInterface {
        if (!$this->isSeekable()) {
            throw new RuntimeException(static::EXCEPTION_NOT_SEEKABLE);
        }

        if (fseek($this->resource, $offset, $whence) === 0) {
            return $this;
        }

        throw new RuntimeException(static::EXCEPTION_SEEK_ERROR);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::tell()
     */
    public function tell(): int {
        $result = ftell($this->resource);

        if (is_int($result)) {
            return $result;
        }

        throw new RuntimeException(static::EXCEPTION_UNABLE_TO_DETERMINE_POSITION);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Stream\StreamInterface::write($string)
     */
    public function write(string $string): int {
        if (!$this->isWritable()) {
            throw new RuntimeException(static::EXCEPTION_NOT_WRITABLE);
        }

        if (false !== $result = fwrite($this->resource, $string)) {
            return $result;
        }

        throw new RuntimeException(static::EXCEPTION_WRITE_ERROR);
    }
}