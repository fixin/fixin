<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Strings extends DoNotCreate {

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