<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Json;

use Fixin\Resource\ResourceInterface;

interface JsonInterface extends ResourceInterface
{
    public const
        DECODING_MAX_DEPTH = 'decodingMaxDepth',
        DECODING_OPTIONS = 'decodingOptions',
        ENCODING_MAX_DEPTH = 'encodingMaxDepth',
        ENCODING_OPTIONS = 'encodingOptions';

    /**
     * Decode JSON string
     *
     * @param string $json
     * @return mixed
     */
    public function decode(string $json);

    /**
     * Encode value to JSON representation
     *
     * @param $value
     * @return string
     */
    public function encode($value): string;
}
