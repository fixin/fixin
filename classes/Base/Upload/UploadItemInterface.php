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

    /**
     * Get error
     *
     * @return int
     */
    public function getError(): int;

    /**
     * Get client filename
     *
     * @return string
     */
    public function getClientFilename(): string;

    /**
     * Get client MIME-type
     *
     * @return string
     */
    public function getClientMimeType(): string;

    /**
     * Get size
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Get temp filename
     *
     * @return string
     */
    public function getTempFilename(): string;

    /**
     * Move file
     *
     * @param string $to
     * @return $this
     */
    public function moveFile(string $to): UploadItemInterface;
}
