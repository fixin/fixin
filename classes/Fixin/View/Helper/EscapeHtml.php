<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

class EscapeHtml extends EscapeHelper
{
    public function escape($value): string
    {
        return $this->escaper->escapeHtml((string) $value);
    }
}
