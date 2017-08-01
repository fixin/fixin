<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Upload;

use Fixin\Resource\PrototypeInterface;

interface UploadItemInterface extends PrototypeInterface
{
    public const
        ERROR = 'error',
        CLIENT_FILENAME = 'clientFilename',
        CLIENT_MIME_TYPE = 'clientMimeType',
        TEMP_FILENAME = 'tempFilename',
        SIZE = 'size';

    public function getError(): int;
    public function getClientFilename(): string;
    public function getClientMimeType(): string;
    public function getSize(): int;
    public function getTempFilename(): string;

    /**
     * @return $this
     */
    public function moveFile(string $to): UploadItemInterface;
}
