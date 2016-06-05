<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Json;

use Fixin\Exception\RuntimeException;
use Fixin\Resource\Resource;

class Json extends Resource implements JsonInterface {

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
     * {@inheritDoc}
     * @see \Fixin\Base\Json\JsonInterface::decode($json)
     */
    public function decode(string $json) {
        $result = json_decode($json, true, $this->decodingMaxDepth, $this->decodingOptions);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new RuntimeException(json_last_error_msg());
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Json\JsonInterface::encode($value)
     */
    public function encode($value): string {
        return json_encode($value, $this->encodingOptions, $this->encodingMaxDepth);
    }

    /**
     * Set decoding max depth
     *
     * @param int $decodingMaxDepth
     */
    protected function setDecodingMaxDepth(int $decodingMaxDepth) {
        $this->decodingMaxDepth = $decodingMaxDepth;
    }

    /**
     * Set decoding options
     *
     * @param int $decodingOptions
     */
    protected function setDecodingOptions(int $decodingOptions) {
        $this->decodingOptions = $decodingOptions;
    }

    /**
     * Set encoding max depth
     *
     * @param int $encodingMaxDepth
     */
    protected function setEncodingMaxDepth(int $encodingMaxDepth) {
        $this->encodingMaxDepth = $encodingMaxDepth;
    }

    /**
     * Set encoding options
     *
     * @param int $encodingOptions
     */
    protected function setEncodingOptions(int $encodingOptions) {
        $this->encodingOptions = $encodingOptions;
    }
}