<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Arrays extends DoNotCreate
{
    /**
     * Get array item
     */
    public static function arrayForKey(array $array, $key): ?array
    {
        $value = $array[$key] ?? null;

        return is_array($value) ? $value : null;
    }

    public static function intersectByKeys(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Set value at path
     */
    public static function set(array &$array, array $path, $data): void
    {
        $current = array_shift($path);

        if ($path) {
            if (!isset($array[$current]) || !is_array($array[$current])) {
                $array[$current] = [];
            }

            static::set($array[$current], $path, $data);

            return;
        }

        $array[$current] = $data;
    }
}
