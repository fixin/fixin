<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Strings extends DoNotCreate {

    const CLASS_NAME_REPLACE = ['.' => '', '-' => '', '_' => ''];
    const CLASS_NAME_WORD_DELIMITERS = '.-_\\';
    const GENERATE_RANDOM_REPLACE = ['/' => '', '+' => '', '=' => ''];
    const METHOD_NAME_REPLACE = ['.' => '', '-' => '', '_' => ''];
    const METHOD_NAME_WORD_DELIMITERS = '.-_';
    const NORMALIZE_LEADING_REPLACE = ["\t" => '    '];

    /**
     * Determine if string begins with a string
     *
     * @param string $string
     * @param string $begin
     * @return bool
     */
    public static function beginsWith(string $string, string $begin): bool {
        return strncmp($string, $begin, strlen($begin)) === 0;
    }

    /**
     * Convert string to "CamelCase" class name
     *
     * @param string $string
     * @return string
     */
    public static function className(string $string): string {
        return strtr(ucwords($string, static::CLASS_NAME_WORD_DELIMITERS), static::CLASS_NAME_REPLACE);
    }

    /**
     * Determine if string ends with a string
     *
     * @param string $string
     * @param string $end
     * @return bool
     */
    public static function endsWith(string $string, string $end): bool {
        return substr_compare($string, $end, -strlen($end)) === 0;
    }

    /**
     * Generate random alpha-numeric string
     *
     * @param int $length
     * @return string
     */
    public static function generateRandom(int $length): string {
        $string = '';

        while (($required = $length - strlen($string)) > 0) {
            $string .= strtr(base64_encode(random_bytes(ceil($required * 0.8))), static::GENERATE_RANDOM_REPLACE);
        }

        return substr($string, 0, $length);
    }

    /**
     * Convert string to "camelCase" method name
     *
     * @param string $string
     * @return string
     */
    public static function methodName(string $string): string {
        return lcfirst(strtr(ucwords($string, static::METHOD_NAME_WORD_DELIMITERS), static::METHOD_NAME_REPLACE));
    }

    /**
     * Replace tabs with spaces in leading of the line
     *
     * @param string $line
     * @return string
     */
    public static function normalizeLeading(string $line): string {
        $indent = strspn($line, " \t");

        return strtr(substr($line, 0, $indent), static::NORMALIZE_LEADING_REPLACE) . mb_substr($line, $indent);
    }

    /**
     * Normalize path to ending with DIRECTORY_SEPARATOR
     *
     * @param string $path
     * @return string
     */
    public static function normalizePath(string $path): string {
        return rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Determine if surrounded by $begin and $end
     *
     * @param string $string
     * @param string $begin
     * @param string $end
     * @return boolean
     */
    public static function surroundedBy(string $string, string $begin, string $end) {
        return static::beginsWith($string, $begin) && static::endsWith($string, $end);
    }
    
    /**
     * Convert "camelCase" or "CamelCase" name to text
     *
     * @param string $string
     * @return string
     */
    public static function textFromCamelCase(string $string): string {
        return preg_replace_callback('/([a-z])([A-Z])/', function($m) {
            return "$m[1] $m[2]";
        }, $string);
    }
}