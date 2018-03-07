<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Header;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class HeaderManager extends Prototype implements HeaderManagerInterface
{
    protected const
        THIS_SETS = [
            self::VALUES => Types::ARRAY
        ];

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
     * @inheritDoc
     */
    public function add(string $name, string $value): HeaderManagerInterface
    {
        $list = (array) ($this->values[$name] ?? []);
        $list[] = $value;

        $this->values[$name] = $list;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clear(): HeaderManagerInterface
    {
        $this->values = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, array $default = []): array
    {
        $values = $this->values[$name] ?? $default;

        if (is_array($values)) {
            return $values;
        }

        return $this->values[$name] = (array) $values;
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return (bool) count($this->values[$name] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function send(): HeaderManagerInterface
    {
        foreach ($this->values as $name => $values) {
            foreach ((array) $values as $value) {
                header("$name: " . $value, false);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, array $values): HeaderManagerInterface
    {
        $this->values[$name] = $values;

        return $this;
    }
}
