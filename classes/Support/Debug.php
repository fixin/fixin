<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class Debug extends DoNotCreate
{
    public static function peek($var): void
    {
        echo Ground::toDebugBlock(VariableInspector::valueInfo($var));
    }
}
