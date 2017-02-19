<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Json;

use Fixin\Base\Json\Exception;
use Fixin\Resource\Resource;

class Json extends Resource implements JsonInterface
{
    /**
     * @var int
     */
    protected $decodingOptions = JSON_BIGINT_AS_STRING;

    /**
     * @var int
     */
    protected $decodingMaxDepth = 512;

    /**
     * @var int
     */
    protected $encodingOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

    /**
     * @var int
     */
    protected $encodingMaxDepth = 512;

    public function decode(string $json)
    {
        $result = json_decode($json, true, $this->decodingMaxDepth, $this->decodingOptions);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new Exception\RuntimeException(json_last_error_msg());
    }

    public function encode($value): string
    {
        return json_encode($value, $this->encodingOptions, $this->encodingMaxDepth);
    }

    protected function setDecodingMaxDepth(int $decodingMaxDepth): void
    {
        $this->decodingMaxDepth = $decodingMaxDepth;
    }

    protected function setDecodingOptions(int $decodingOptions): void
    {
        $this->decodingOptions = $decodingOptions;
    }

    protected function setEncodingMaxDepth(int $encodingMaxDepth): void
    {
        $this->encodingMaxDepth = $encodingMaxDepth;
    }

    protected function setEncodingOptions(int $encodingOptions): void
    {
        $this->encodingOptions = $encodingOptions;
    }
}
