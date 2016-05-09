<?php

namespace Fixin\Support;

trait ToStringTrait {

    /**
     * @return string
     */
    public function __toString() {
        $info = '';

        foreach ($this as $key => $value) {
            // TODO: exchange print_r
            $info .= "\t{$key}: " . str_replace("\n", "\n\t", print_r($value, true)) . "\n";
        }

        return '[' . get_class($this) . "]\n{$info}\n";
    }
}