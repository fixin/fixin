<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class CodeInspector extends DoNotCreate
{
    /**
     * Get source code of function
     *
     * @param string|\Closure $function
     */
    public static function functionSource($function): string
    {
        // File content
        $reflection = new \ReflectionFunction($function);
        $file = file($reflection->getFileName()) ?: [];
        $lines = array_slice($file, $startLine = $reflection->getStartLine(), $reflection->getEndLine() - $startLine);

        // Last row
        $last = array_pop($lines);
        array_push($lines, rtrim(mb_substr($last, 0, mb_strrpos($last, '}'))));

        return Strings::removeMainIndent($lines);
    }
}
