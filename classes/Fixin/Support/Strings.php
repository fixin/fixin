<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Strings extends DoNotCreate {

    const SOURCE_REPLACE = ["\t" => '    '];

    /**
     * Replace tabs with spaces in leading of the line
     *
     * @param string $line
     * @return string
     */
    public static function normalizeLeading(string $line): string {
        $indent = strspn($line, " \t");

        return strtr(substr($line, 0, $indent), static::SOURCE_REPLACE) . mb_substr($line, $indent);
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