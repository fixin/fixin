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
use Fixin\Support\Types;

class Json extends Resource implements JsonInterface
{
    protected const
        THIS_SETS = [
            self::DECODING_MAX_DEPTH => Types::INT,
            self::DECODING_OPTIONS => Types::INT,
            self::ENCODING_MAX_DEPTH => Types::INT,
            self::ENCODING_OPTIONS => Types::INT
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
     * @throws Exception\DecodingFailureException
     */
    public function decode(string $json)
    {
        $result = json_decode($json, true, $this->decodingMaxDepth, $this->decodingOptions);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new Exception\DecodingFailureException(json_last_error_msg(), json_last_error());
    }

    /**
     * @throws Exception\EncodingFailureException
     */
    public function encode($value): string
    {
        $result = json_encode($value, $this->encodingOptions, $this->encodingMaxDepth);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new Exception\EncodingFailureException(json_last_error_msg(), json_last_error());
    }
}
