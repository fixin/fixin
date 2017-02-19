<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Json;

use Fixin\Resource\ResourceInterface;

interface JsonInterface extends ResourceInterface
{
    public const
        OPTION_DECODING_MAX_DEPTH = 'decodingMaxDepth',
        OPTION_DECODING_OPTIONS = 'decodingOptions',
        OPTION_ENCODING_MAX_DEPTH = 'encodingMaxDepth',
        OPTION_ENCODING_OPTIONS = 'encodingOptions';

    /**
     * Decode JSON string
     */
    public function decode(string $json);

    /**
     * Encode value to JSON representation
     */
    public function encode($value): string;
}
