<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Json;

use Fixin\ResourceManager\ResourceInterface;

interface JsonInterface extends ResourceInterface {

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