<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class VariableContainer extends Prototype implements VariableContainerInterface
{
    use ContainerTrait;

    protected const
        THIS_SETS = [
            self::VALUES => Types::ARRAY
        ];

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @inheritDoc
     */
    public function clear(): VariableContainerInterface
    {
        $this->values = [];

        $this->modified = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): VariableContainerInterface
    {
        unset($this->values[$key]);

        $this->modified = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $keys): VariableContainerInterface
    {
        $this->values = array_diff_key($this->values, array_flip($keys));
    }

    /**
     * @inheritDoc
     */
    public function isModified(): bool
    {
        return $this->modified;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->values);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): VariableContainerInterface
    {
        $this->values[$key] = $value;

        $this->modified = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setModified(bool $modified): VariableContainerInterface
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(array $values): VariableContainerInterface
    {
        $this->values = $values + $this->values;

        $this->modified = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->values = unserialize($serialized);
    }
}