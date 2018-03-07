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

class MultiLevelDictionary extends Resource implements DictionaryInterface
{
    public const
        LEVELS = 'levels';

    protected const
        INVALID_LEVEL_TYPE_EXCEPTION = "Invalid level type at '%s'",
        THIS_SETS = [
            self::LEVELS => self::USING_SETTER
        ];

    /**
     * @var DictionaryInterface[]
     */
    protected $levels;

    /**
     * @inheritDoc
     */
    public function clear(): DictionaryInterface
    {
        foreach ($this->levels as $level) {
            $level->clear();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $step = 1): int
    {
        $levels = $this->levels;
        $value = reset($levels)->decrement($key, $step);

        while ($level = next($levels)) {
            $level->set($key, $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): DictionaryInterface
    {
        foreach ($this->levels as $level) {
            $level->delete($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $keys): DictionaryInterface
    {
        foreach ($this->levels as $level) {
            $level->deleteMultiple($keys);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        foreach ($this->levels as $level) {
            if (null !== $value = $level->get($key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(array $keys): array
    {
        $result = [];

        foreach ($this->levels as $level) {
            $values = $level->getMultiple($keys);

            $result += $values;

            if (false === $keys = array_diff($keys, array_keys($values))) {
                break;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $step = 1): int
    {
        $levels = $this->levels;
        $value = reset($levels)->increment($key, $step);

        while ($level = next($levels)) {
            $level->set($key, $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value, int $expireTime = 0): DictionaryInterface
    {
        foreach ($this->levels as $level) {
            $level->set($key, $value, $expireTime);
        }

        return $this;
    }

    /**
     * Set levels
     *
     * @param array $levels
     * @throws Exception\InvalidArgumentException
     */
    protected function setLevels(array $levels): void
    {
        $this->levels = [];

        foreach ($levels as $name => $level) {
            if (is_string($level)) {
                $this->levels[$name] = $this->resourceManager->get($level, DictionaryInterface::class);

                continue;
            }

            if ($level instanceof DictionaryInterface) {
                $this->levels[$name] = $level;

                continue;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::INVALID_LEVEL_TYPE_EXCEPTION, $name));
        }
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(array $items, int $expireTime = 0): DictionaryInterface
    {
        foreach ($this->levels as $level) {
            $level->setMultiple($items, $expireTime);
        }

        return $this;
    }
}