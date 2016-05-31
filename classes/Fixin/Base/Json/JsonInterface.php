<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Json;

use Fixin\Resource\ResourceInterface;

interface JsonInterface extends ResourceInterface {

    const OPTION_DECODING_MAX_DEPTH = 'decodingMaxDepth';
    const OPTION_DECODING_OPTIONS = 'decodingOptions';
    const OPTION_ENCODING_MAX_DEPTH = 'encodingMaxDepth';
    const OPTION_ENCODING_OPTIONS = 'encodingOptions';

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
     * @param mixed $value
     * @return string
     */
    public function encode($value): string;
}