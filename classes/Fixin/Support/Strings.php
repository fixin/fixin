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
    const METHOD_NAME_REPLACE = ['.' => '', '-' => '', '_' => ''];
    const METHOD_NAME_WORD_DELIMITERS = '.-_';
    const NORMALIZE_LEADING_REPLACE = ["\t" => '    '];

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
}