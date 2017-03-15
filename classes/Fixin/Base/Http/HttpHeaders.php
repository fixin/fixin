<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Http;

use Fixin\Resource\Prototype;

class HttpHeaders extends Prototype implements HttpHeadersInterface
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @return static
     */
    public function add(string $name, string $value): HttpHeadersInterface
    {
        $list = (array) ($this->values[$name] ?? []);
        $list[] = $value;

        $this->values[$name] = $list;

        return $this;
    }

    /**
     * @return static
     */
    public function clear(): HttpHeadersInterface
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
     * @return static
     */
    public function send(): HttpHeadersInterface
    {
        foreach ($this->values as $name => $values) {
            foreach ((array) $values as $value) {
                header("$name: " . $value, false);
            }
        }

        return $this;
    }

    /**
     * @return static
     */
    public function set(string $name, array $values): HttpHeadersInterface
    {
        $this->values[$name] = $values;

        return $this;
    }

    protected function setValues(array $values): void
    {
        $this->values = $values;
    }
}
