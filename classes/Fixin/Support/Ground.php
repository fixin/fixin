<?php

namespace Fixin\Support;

class Ground {

    use DoNotCreateTrait;

    /**
     * @var bool
     */
    protected static $isConsole;

    /**
     * Checks if running in CLI mode
     *
     * @return boolean
     */
    static public function isConsole() {
        return static::$isConsole ?? (static::$isConsole = PHP_SAPI == 'cli');
    }
}
