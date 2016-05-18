<?php

namespace Fixin\Base\Config;
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use Fixin\Base\Exception\InvalidKeyException;
use Fixin\Support\ContainerInterface;
use Fixin\Support\ToStringTrait;

class Config implements ContainerInterface, \Iterator {

    use ToStringTrait;

    const EXCEPTION_NOT_DEFINED = "Value for '%s' is not defined";

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     * @see Iterator::current()
     */
    public function current() {
        return current($this->config);
    }

    /**
     * Get value for name
     *
     * @param string $name
     * @throws InvalidKeyException
     * @return mixed
     */
    public function get(string $name) {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        throw new InvalidParameterException(sprintf(static::EXCEPTION_NOT_DEFINED, $name));
    }

    /**
     * Check if name has value
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool {
        return isset($this->config[$name]);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::key()
     */
    public function key() {
        return key($this->config);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::next()
     */
    public function next() {
        next($this->config);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind() {
        reset($this->config);
    }

    /**
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid() {
        return key($this->config) !== null;
    }
}