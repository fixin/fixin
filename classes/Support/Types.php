<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

interface Types
{
    public const
        ANY = 1,
        ARRAY = 2,
        BOOL = 3,
        CALLABLE = 4,
        FLOAT = 5,
        INT = 6,
        NULL = 7,
        NUMERIC = 8,
        OBJECT = 9,
        SCALAR = 10,
        STRING = 11,

        CHECK_FUNCTIONS = [
            self::ANY => true,
            self::ARRAY => 'is_array',
            self::BOOL => 'is_bool',
            self::CALLABLE => 'is_callable',
            self::FLOAT => 'is_float',
            self::INT => 'is_int',
            self::NULL => 'is_null',
            self::NUMERIC => 'is_numeric',
            self::OBJECT => 'is_object',
            self::SCALAR => 'is_scalar',
            self::STRING => 'is_string'
        ],

        NAME_LIST = [
            self::ANY => 'any',
            self::ARRAY => 'array',
            self::BOOL => 'bool',
            self::CALLABLE => 'callable',
            self::FLOAT => 'float',
            self::INT => 'int',
            self::NULL => 'null',
            self::NUMERIC => 'numeric',
            self::OBJECT => 'object',
            self::SCALAR => 'scalar',
            self::STRING => 'string'
        ];
}
