<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class Strings extends DoNotCreate
{
    protected const
        CLASS_NAME_REPLACE = ['.' => '', '-' => '', '_' => ''],
        CLASS_NAME_WORD_DELIMITERS = '.-_\\',
        GENERATE_RANDOM_REPLACE = ['/' => '', '+' => '', '=' => ''],
        METHOD_NAME_REPLACE = ['.' => '', '-' => '', '_' => ''],
        METHOD_NAME_WORD_DELIMITERS = '.-_',
        NORMALIZE_LEADING_REPLACE = ["\t" => '    '];

    /**
     * Convert "camelCase" or "CamelCase" name to text
     */
    public static function camelCasedToText(string $string): string
    {
        return preg_replace_callback('/([a-z])([A-Z])/', function($tag) {
            return "$tag[1] $tag[2]";
        }, $string);
    }

    /**
     * Get extension of a filename
     */
    public static function extractExtension(string $path): ?string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Generate random alpha-numeric string
     */
    public static function generateRandomAlnum(int $length): string
    {
        $string = '';

        while (($required = $length - strlen($string)) > 0) {
            $string .= strtr(base64_encode(random_bytes(ceil($required * 0.8))), static::GENERATE_RANDOM_REPLACE);
        }

        return substr($string, 0, $length);
    }

    /**
     * Determine if string begins with a string
     */
    public static function isBeginningWith(string $string, string $begin): bool
    {
        return strncmp($string, $begin, strlen($begin)) === 0;
    }

    /**
     * Determine if string ends with a string
     */
    public static function isEndingWith(string $string, string $end): bool
    {
        return substr_compare($string, $end, -strlen($end)) === 0;
    }

    /**
     * Determine if surrounded by $begin and $end
     */
    public static function isSurroundedBy(string $string, string $begin, string $end): bool
    {
        return static::isBeginningWith($string, $begin) && static::isEndingWith($string, $end);
    }

    /**
     * Replace tabs with spaces in leading of the line
     */
    public static function normalizeLeading(string $line): string
    {
        $indent = strspn($line, " \t");

        return strtr(substr($line, 0, $indent), static::NORMALIZE_LEADING_REPLACE) . mb_substr($line, $indent);
    }

    /**
     * Normalize path to ending with DIRECTORY_SEPARATOR
     */
    public static function normalizePath(string $path): string
    {
        return rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    public static function removeMainIndent(array $lines): string
    {
        // Indents
        $left = PHP_INT_MAX;

        foreach ($lines as &$line) {
            if (trim($line) === '') {
                continue;
            }

            $line = static::normalizeLeading($line);
            $left = min($left, strspn($line, ' '));
        }

        foreach ($lines as &$line) {
            $line = mb_substr($line, $left);
        }

        return implode('', $lines);
    }

    /**
     * Convert string to "CamelCase" class name
     */
    public static function toClassName(string $string): string
    {
        return strtr(ucwords($string, static::CLASS_NAME_WORD_DELIMITERS), static::CLASS_NAME_REPLACE);
    }

    /**
     * Convert string to "camelCase" method name
     */
    public static function toMethodName(string $string): string
    {
        return lcfirst(strtr(ucwords($string, static::METHOD_NAME_WORD_DELIMITERS), static::METHOD_NAME_REPLACE));
    }
}
