<?php

namespace Fixin\Support;

class Ground extends DoNotCreate {

    /**
     * @var bool
     */
    protected static $isConsole;

    /**
     * Checks if running in CLI mode
     *
     * @return boolean
     */
    public static function isConsole() {
        return static::$isConsole ?? (static::$isConsole = PHP_SAPI == 'cli');
    }
}