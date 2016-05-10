<?php

namespace Fixin\Base\Configurable;

interface ConfigurableInterface {

    /**
     * Configures object
     *
     * @param array $config
     * @return self
     */
    public function configure(array $config);
}