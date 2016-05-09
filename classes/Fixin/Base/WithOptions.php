<?php

namespace Fixin\Base;

abstract class WithOptions {

    /**
     * @param array $options
     */
    public function __construct(array $options = []) {
        $this->setOptions($options);
    }

    /**
     * Setting options using setters
     *
     * @param array $options
     * @return \Fixin\Base\WithOptions
     */
    public function setOptions(array $options) {
        foreach ($options as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
            else {
                throw new Exception\InvalidKeyException('Invalid option: ' . $key);
            }
        }

        return $this;
    }
}