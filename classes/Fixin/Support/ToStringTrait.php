<?php

namespace Fixin\Support;

trait ToStringTrait {

    /**
     * @return string
     */
    public function __toString() {
        $info = '[' . get_class($this) . "] {\n";

        foreach (get_object_vars($this) as $key => $value) {
            // TODO: replace print_r
            $info .= "\t{$key}: " . str_replace("\n", "\n\t", print_r($value, true)) . "\n";
        }

        $info .= "}\n";

        return Ground::isConsole() ? $info : '<pre>' . htmlspecialchars($info) . '</pre>';
    }
}
