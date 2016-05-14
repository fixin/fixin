<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Configurable;

interface ConfigurableInterface {

    /**
     * Configure object
     *
     * @param array $config
     * @return self
     */
    public function configure(array $config);
}