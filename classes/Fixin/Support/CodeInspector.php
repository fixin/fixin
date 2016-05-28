<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class CodeInspector extends DoNotCreate {

    const SOURCE_REPLACE = ["\t" => '    '];

    /**
     * Remove indent from lines
     *
     * @param array $lines
     * @return mixed
     */
    protected static function removeIndent(array $lines) {
        // Indents
        $source = [];
        $max = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $source[] = "\n";

                continue;
            }

            $indent = strspn($line, " \t");
            $leading = strtr(substr($line, 0, $indent), static::SOURCE_REPLACE);
            $source[] = $leading . mb_substr($line, $indent);
            $max = isset($max) ? min($max, strlen($leading)) : strlen($leading);
        }

        return implode('', array_map(function($item) use ($max) {
            return mb_substr($item, $max);
        }, $source));
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