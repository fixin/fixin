<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Dictionary;

use Fixin\Resource\Resource;
use Fixin\Support\DateTimes;

class RuntimeDictionary extends Resource implements DictionaryInterface
{
    protected const
        NON_NUMERIC_VALUE_EXCEPTION = 'The value is non-numeric';

    /**
     * @var array
     */
    protected $expires = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @inheritDoc
     */
    public function clear(): DictionaryInterface
    {
        $this->items = [];
        $this->expires = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $step = 1): int
    {
        if (null !== $value = $this->get($key)) {
            if (is_numeric($value)) {
                return $this->items[$key] = $value > $step ? $value - $step : 0;
            }

            throw new Exception\UnexpectedValueException(static::NON_NUMERIC_VALUE_EXCEPTION);
        }

        $this->set($key, 0);

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): DictionaryInterface
    {
        unset($this->items[$key]);
        unset($this->expires[$key]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $keys): DictionaryInterface
    {
        $this->items = array_diff_key($this->items, array_flip($keys));
        $this->expires = array_diff_key($this->expires, array_flip($keys));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return $this->getMultiple([$key])[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(array $keys): array
    {
        $values = [];
        $now = new \DateTimeImmutable();

        foreach ($keys as $key) {
            if (array_key_exists($key, $this->expires)) {
                if (($this->expires[$key] ?? $now) < $now) {
                    unset($this->items[$key]);
                    unset($this->expires[$key]);

                    continue;
                }

                $values[$key] = $this->items[$key];
            }
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $step = 1): int
    {
        if (null !== $value = $this->get($key)) {
            if (is_numeric($value)) {
                return $this->items[$key] = $value + $step;
            }

            throw new Exception\UnexpectedValueException(static::NON_NUMERIC_VALUE_EXCEPTION);
        }

        $this->set($key, $step);

        return $step;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value, ?int $expireTime = null): DictionaryInterface
    {
        $this->items[$key] = $value;
        $this->setExpireTime($key, $expireTime);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setExpireTime(string $key, ?int $expireTime = 0): DictionaryInterface
    {
        $this->expires[$key] = $expireTime ? DateTimes::fromExpireTime($expireTime) : null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(array $items, ?int $expireTime = null): DictionaryInterface
    {
        $this->items = $items + $this->items;

        if ($expireTime) {
            $this->expires = array_fill_keys(array_keys($items), DateTimes::fromExpireTime($expireTime));

            return $this;
        }

        $this->expires = array_diff_key($this->expires, $items);

        return $this;
    }
}