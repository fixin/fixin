<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

trait ContainerTrait
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @inheritDoc
     */
    public function __debugInfo()
    {
        return $this->values;
    }

    /**
     * Get value for key or return default value for non-set key
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    /**
     * Determine the key has value
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->values[$key]);
    }
}
