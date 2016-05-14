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
        return Ground::isConsole()
        ? htmlspecialchars_decode(strip_tags(Ground::valueInfo($this)))
        : '<div style="font-family: monospace; white-space: pre; color: #000; line-height: 1.05">' . Ground::valueInfo($this) . '</div>';
    }

    /**
     * Debug info
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function __debugInfo(): array {
        return (array) $this;
    }
}
