<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

class EscapeJs extends AbstractEscapeHelper
{
    /**
     * @inheritDoc
     */
    public function escape($value): string
    {
        return $this->escaper->escapeJs((string) $value);
    }
}
