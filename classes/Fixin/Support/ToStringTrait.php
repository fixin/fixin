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
     */
    public function __toString(): string {
        return Ground::isConsole()
        ? htmlspecialchars_decode(strip_tags(Ground::valueInfo($this)))
        : '<div style="font-family: monospace; white-space: pre">' . Ground::valueInfo($this) . '</div>';
    }

    /**
     * Debug info
     *
     * @return array
     */
    public function __debugInfo(): array {
        return get_object_vars($this);
    }
}
