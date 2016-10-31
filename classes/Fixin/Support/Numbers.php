<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Numbers extends DoNotCreate {

    /**
     * Determine if value is int
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isInt($value) {
        return is_string($value) ? (string) intval($value) === $value : $value !== null && intval($value) == $value;
    }
}