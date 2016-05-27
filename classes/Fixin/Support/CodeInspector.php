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
     * @param unknown $function
     * @return mixed
     */
    public static function source($function) {
        // File content
        $reflection = new \ReflectionFunction($function);
        $file = file($reflection->getFileName());
        $lines = array_slice($file, $startLine = $reflection->getStartLine(), $reflection->getEndLine() - $startLine);

        // Last row
        $last = array_pop($lines);
        array_push($lines, rtrim(mb_substr($last, 0, mb_strrpos($last, '}'))));

        // Indents
        $source = [];

        foreach ($lines as $line) {
            if (mb_strlen($line) === $indent = strspn($line, " \t")) {
                $source[] = "";

                continue;
            }

            $leading = strtr(substr($line, 0, $indent), static::SOURCE_REPLACE);
            $source[] = $leading . mb_substr($line, $indent);
            $max = isset($max) ? min($max, strlen($leading)) : strlen($leading);
        }

        return array_reduce($source, function($result, $item) use ($max) {
            return $result . mb_substr($item, $max);
        }, '');
    }
}