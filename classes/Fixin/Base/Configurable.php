<?php

namespace Fixin\Base;

abstract class Configurable {

    /**
     * @param array $config
     */
    public function __construct(array $config = []) {
        $this->configure($config);
    }

    /**
     * Configure using setters
     *
     * @param array $config
     * @throws Exception\InvalidKeyException
     * @return self
     */
    protected function configure(array $config) {
        foreach ($config as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
            else {
                throw new Exception\InvalidParameterException("Invalid option: '$key'");
            }
        }

        return $this;
    }
}