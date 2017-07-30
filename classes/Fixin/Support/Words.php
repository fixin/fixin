<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class Words extends DoNotCreate
{
    protected const
        PLURAL_MODE = 2,
        PLURALIZER_CASES = [
            self::SINGULAR_MODE => [
                // -ch, -s, -x, -z
                '(.+)(ch|s|x|z)es' => '\1\2',

                // -f, -fe
                '(.+)v(e?)s' => '\1f\2',

                // -y
                '(.+)([aeiou])ys' => '\1\2y',
                '(.+)ies' => '\1y',

                // -o
                '(phot|pr|zer)os' => '\1o',
                '(.+)([^aeiou])es' => '\1\2o',

                // default
                '(.+)s' => '\1'
            ],
            self::PLURAL_MODE => [
                // -ch, -s, -x, -z
                '(.+)(ch|s|x|z)' => '\1\2es',

                // -f, -fe
                '(.+)f(e?)' => '\1v\2s',

                // -y
                '(.+)([aeiou])y' => '\1\2ys',
                '(.+)y' => '\1ies',

                // -o
                '(phot|pr|zer)o' => '\1s',
                '(.+)([^aeiou])o' => '\1\2es',

                // default
                '(.+)' => '\1s'
            ]
        ],
        PLURALIZER_SPECIALS = [
            self::SINGULAR_MODE => [
                'children' => 'child',
                'data' => 'data',
                'info' => 'info',
                'information' => 'information',
                'men' => 'man',
                'music' => 'music',
                'toys' => 'toy',
                'women' => 'woman',
            ],
            self::PLURAL_MODE => [
                'child' => 'children',
                'data' => 'data',
                'info' => 'info',
                'information' => 'information',
                'man' => 'men',
                'music' => 'music',
                'toy' => 'toys',
                'woman' => 'women',
            ]
        ],
        SINGULAR_MODE = 1
    ;

    protected static $cache = [];

    protected static function pluralizer(string $string, int $mode): string
    {
        $tags = explode('_', $string);
        $last = array_pop($tags);
        $lastLc = mb_strtolower($last);

        $word = static::$cache[$mode][$lastLc] ?? static::$cache[$mode][$lastLc] = static::PLURALIZER_SPECIALS[$mode][$lastLc] ?? Strings::patternReplace($lastLc, static::PLURALIZER_CASES[$mode]);
        $tags[] = Strings::matchCases($word, $last);

        return implode('_', $tags);
    }

    public static function toPlural(string $string): string
    {
        return static::pluralizer($string, static::PLURAL_MODE);
    }

    public static function toSingular(string $string): string
    {
        return static::pluralizer($string, static::SINGULAR_MODE);
    }
}
