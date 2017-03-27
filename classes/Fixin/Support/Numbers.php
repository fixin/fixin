<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class Numbers extends DoNotCreate
{
    /**
     * Determine if value is int
     */
    public static function isInt($value): bool
    {
        return is_string($value) ? (string) intval($value) === $value : $value !== null && intval($value) == $value;
    }
}
