<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

class EscapeJs extends EscapeHelper {

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Helper\EscapeHelper::escape($value)
     */
    public function escape($value): string {
        return $this->escaper->escapeJs((string) $value);
    }
}