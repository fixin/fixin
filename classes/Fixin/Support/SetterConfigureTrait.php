<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

use Fixin\Base\Exception\InvalidParameterException;

trait SetterConfigureTrait {

    /**
     * Configure by setters
     *
     * @param \Traversable $config
     * @throws InvalidParameterException
     * @return self
     */
    public function configure(\Traversable $config) {
        foreach ($config as $key => $value) {
            $method = 'set' . $key;

            if (method_exists($this, $method)) {
                $this->$method($value);

                continue;
            }

            throw new InvalidParameterException("Invalid option: '$key'");
        }

        return $this;
    }
}