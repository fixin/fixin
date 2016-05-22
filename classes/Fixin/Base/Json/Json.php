<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Json;

use Fixin\ResourceManager\Resource;

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
    protected $encodingOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    /**
     * @var int
     */
    protected $encodingMaxDepth = 512;

    /**
     * @param int $decodingMaxDepth
     */
    protected function _setupDecodingMaxDepth(int $decodingMaxDepth) {
        $this->decodingMaxDepth = $decodingMaxDepth;
    }

    /**
     * @param int $decodingOptions
     */
    protected function _setupDecodingOptions(int $decodingOptions) {
        $this->decodingOptions = $decodingOptions;
    }

    /**
     * @param int $encodingMaxDepth
     */
    protected function _setupEncodingMaxDepth(int $encodingMaxDepth) {
        $this->encodingMaxDepth = $encodingMaxDepth;
    }

    /**
     * @param int $encodingOptions
     */
    protected function _setupEncodingOptions(int $encodingOptions) {
        $this->encodingOptions = $encodingOptions;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Json\JsonInterface::decode($json)
     */
    public function decode(string $json) {
        return json_decode($json, true, $this->decodingMaxDepth, $this->decodingOptions);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Json\JsonInterface::encode($value)
     */
    public function encode($value): string {
        return json_encode($value, $this->encodingOptions, $this->encodingMaxDepth);
    }

    /**
     * Get decoding options
     *
     * @return int
     */
    public function getDecodingOptions(): int {
        return $this->decodingOptions;
    }

    /**
     * Get encoding options
     *
     * @return int
     */
    public function getEncodingOptions(): int {
        return $this->encodingOptions;
    }
}