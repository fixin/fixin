<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Json;

use Fixin\Resource\Resource;

class Json extends Resource implements JsonInterface
{
    protected const
        THIS_SETS = [
            self::DECODING_MAX_DEPTH => self::INT_TYPE,
            self::DECODING_OPTIONS => self::INT_TYPE,
            self::ENCODING_MAX_DEPTH => self::INT_TYPE,
            self::ENCODING_OPTIONS => self::INT_TYPE
        ];

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

    /**
     * @throws Exception\RuntimeException
     */
    public function decode(string $json)
    {
        $result = json_decode($json, true, $this->decodingMaxDepth, $this->decodingOptions);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new Exception\RuntimeException(json_last_error_msg(), json_last_error());
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function encode($value): string
    {
        $result = json_encode($value, $this->encodingOptions, $this->encodingMaxDepth);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new Exception\RuntimeException(json_last_error_msg(), json_last_error());
    }
}
