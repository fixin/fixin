<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

trait ToStringTrait {

    /**
     * Return readable list of variables of instance
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __toString(): string {
        $items = method_exists($this, '__debugInfo') ? $this->__debugInfo() : (array) $this;
        $description = get_class($this) . ' {' . ($items ? "\n" . VariableInspector::itemsInfo($items, '#444') : '') . '}';

        return Ground::isConsole()
            ? htmlspecialchars_decode(strip_tags($description))
            : '<div style="font-family: monospace; white-space: pre; color: #000; line-height: 1.05">' . $description . '</div>';
    }
}
