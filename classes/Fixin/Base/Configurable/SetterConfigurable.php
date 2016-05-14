<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Configurable;

use Fixin\Base\Exception\InvalidParameterException;

class SetterConfigurable implements ConfigurableInterface {

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Configurable\ConfigurableInterface::configure()
     */
    public function configure(array $config) {
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