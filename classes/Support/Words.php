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
        NUMBER_CASES = [
            self::SINGULAR_MODE => [
                // -x
                '(.+)ices' => '\1ex',

                // -ch, -s, -x, -z
                '(.+)(ch|s|x|z)es' => '\1\2',

                // -f, -fe
                '(.+)v(e?)s' => '\1f',

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
        PLURAL_MODE = 11,
        SINGULAR_MODE = 1,
        SPECIAL_NUMBER_CASES = [
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
        WORD_SEPARATORS = ' ._:/\\|'
    ;

    /**
     * @var array
     */
    protected static $numberCaseCache = [];

    /**
     * Convert number case
     *
     * @param string $string
     * @param int $mode
     * @return string
     */
    protected static function convertNumberCase(string $string, int $mode): string
    {
        $prefixSize = 0;
        $length = strlen($string);

        while ($length - $prefixSize > $count = strcspn($string, static::WORD_SEPARATORS, $prefixSize)) {
            $prefixSize += $count + 1;
        }

        $last = substr($string, $prefixSize);

        $word = static::$numberCaseCache[$mode][$last] ?? static::$numberCaseCache[$mode][$last] = static::SPECIAL_NUMBER_CASES[$mode][$last] ?? Strings::patternReplace($last, static::NUMBER_CASES[$mode]);

        return substr($string, 0, $prefixSize) . $word;
    }

    /**
     * Get plural variation of a word
     *
     * @param string $string
     * @return string
     */
    public static function toPlural(string $string): string
    {
        return static::convertNumberCase($string, static::PLURAL_MODE);
    }

    /**
     * Get singular variation of a word
     *
     * @param string $string
     * @return string
     */
    public static function toSingular(string $string): string
    {
        return static::convertNumberCase($string, static::SINGULAR_MODE);
    }
}
