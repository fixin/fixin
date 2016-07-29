<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Arrays extends DoNotCreate {

    /**
     * Get array item
     *
     * @param array $array
     * @param string|int $key
     * @return mixed|null
     */
    public static function arrayForKey(array $array, $key) {
        $value = $array[$key] ?? null;

        return is_array($value) ? $value : null;
    }

    /**
     * Intersect by keys
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function intersectByKeys(array $array, array $keys): array {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Set value at path
     *
     * @param array $array
     * @param array $path
     * @param mixed $data
     */
    public static function set(array &$array, array $path, $data) {
        $current = array_shift($path);

        if (count($path)) {
            if (!isset($array[$current]) || !is_array($array[$current])) {
                $array[$current] = [];
            }

            static::set($array[$current], $path, $data);

            return;
        }

        $array[$current] = $data;
    }
}