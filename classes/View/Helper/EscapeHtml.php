<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

class EscapeHtml extends AbstractEscapeHelper
{
    /**
     * @inheritDoc
     */
    public function escape($value): string
    {
        return $this->escaper->escapeHtml((string) $value);
    }
}
