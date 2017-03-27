<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

class JsVariable extends EscapeHelper
{
    public function escape($value): string
    {
        return $this->escaper->escapeJsVariable($value);
    }
}
