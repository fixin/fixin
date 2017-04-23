<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Upload;

use Fixin\Base\Upload\Exception;
use Fixin\Resource\Prototype;
use Fixin\Support\DebugDescriptionTrait;
use Fixin\Support\Types;

class UploadItem extends Prototype implements UploadItemInterface
{
    use DebugDescriptionTrait;

    protected const
        FILE_MOVE_FAILURE_EXCEPTION = "Move file failure from '%s' to '%s'",
        THIS_SETS = [
            self::ERROR => Types::INT,
            self::CLIENT_FILENAME => Types::STRING,
            self::CLIENT_MIME_TYPE => Types::STRING,
            self::TEMP_FILENAME => Types::STRING,
            self::SIZE => Types::INT
        ];

    protected $error;
    protected $clientFilename;
    protected $clientMimeType;
    protected $tempFilename;
    protected $size;

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): string
    {
        return $this->clientFilename;
    }

    public function getClientMimeType(): string
    {
        return $this->clientMimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getTempFilename(): string
    {
        return $this->tempFilename;
    }

    public function moveFile(string $destination): UploadItemInterface
    {
        if (move_uploaded_file($this->tempFilename, $destination)) {
            return $this;
        }

        throw new Exception\FileMoveFailureException(sprintf(static::FILE_MOVE_FAILURE_EXCEPTION, $this->tempFilename, $destination));
    }
}
