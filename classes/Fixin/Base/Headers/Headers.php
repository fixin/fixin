<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Headers;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class Headers extends Prototype implements HeadersInterface
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
     * @return $this
     */
    public function add(string $name, string $value): HeadersInterface
    {
        $list = (array) ($this->values[$name] ?? []);
        $list[] = $value;

        $this->values[$name] = $list;

        return $this;
    }

    /**
     * @return $this
     */
    public function clear(): HeadersInterface
    {
        $this->values = [];

        return $this;
    }

    public function get(string $name, array $default = []): array
    {
        $values = $this->values[$name] ?? $default;

        if (is_array($values)) {
            return $values;
        }

        return $this->values[$name] = (array) $values;
    }

    public function has(string $name): bool
    {
        return (bool) count($this->values[$name] ?? null);
    }

    /**
     * @return $this
     */
    public function send(): HeadersInterface
    {
        foreach ($this->values as $name => $values) {
            foreach ((array) $values as $value) {
                header("$name: " . $value, false);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function set(string $name, array $values): HeadersInterface
    {
        $this->values[$name] = $values;

        return $this;
    }
}
