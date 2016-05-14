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
    public function __toString() {
        $info = get_class($this) . " {\n";
        $items = [];

        foreach (get_object_vars($this) as $key => $value) {
            $items[] = Ground::valueInfo($key) . ': ' . strtr(Ground::valueInfo($value, '"'), ["\n" => "\n    "]);
        }

        $info .= $items ? "    " . implode(",\n    ", $items) . "\n}\n" : '';

        return Ground::isConsole() ? $info : '<div style="font-family: monospace; white-space: pre">' . htmlspecialchars($info) . '</div>';
    }
}
