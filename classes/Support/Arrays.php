<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class Arrays extends DoNotCreate
{
    /**
     * Get array item
     */
    public static function getArrayForKey(array $array, $key): ?array
    {
        $value = $array[$key] ?? null;

        return is_array($value) ? $value : null;
    }

    public static function intersectByKeyList(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Set value at path
     */
    public static function setValueAtPath(array &$array, array $path, $data): void
    {
        $current = array_shift($path);

        if ($path) {
            if (!isset($array[$current]) || !is_array($array[$current])) {
                $array[$current] = [];
            }

            static::setValueAtPath($array[$current], $path, $data);

            return;
        }

        $array[$current] = $data;
    }
}
