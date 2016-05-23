<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

class EscapeHtml extends EscapeHelper {

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Helper\EscapeHelper::escape($value)
     */
    public function escape(string $value): string {
        return $this->escaper->escapeHtml($value);
    }
}