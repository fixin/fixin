<?php

namespace Fixin\Config;

use Fixin\Support\ToStringTrait;

class Config extends \stdClass {

    use ToStringTrait;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __get($name) {
        throw new Exception\InvalidKeyException();
    }
}