<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class CodeInspector extends DoNotCreate {


    /**
     * Remove indent from lines
     *
     * @param array $lines
     * @return mixed
     */
    protected static function removeIndent(array $lines) {
        // Indents
        $left = PHP_INT_MAX;

        foreach ($lines as &$line) {
            if (trim($line) === '') {
                continue;
            }

            $line = Strings::normalizeLeading($line);
            $left = min($left, strspn($line, ' '));
        }

        foreach ($lines as &$line) {
            $line = mb_substr($line, $left);
        }

        return implode('', $lines);
    }

    /**
     * Get source code of function
     *
     * @param string|\Closure $function
     * @return mixed
     */
    public static function functionSource($function) {
        // File content
        $reflection = new \ReflectionFunction($function);
        $file = file($reflection->getFileName());
        $lines = array_slice($file, $startLine = $reflection->getStartLine(), $reflection->getEndLine() - $startLine);

        // Last row
        $last = array_pop($lines);
        array_push($lines, rtrim(mb_substr($last, 0, mb_strrpos($last, '}'))));

        return static::removeIndent($lines);
    }
}