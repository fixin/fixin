<?php

namespace Fixin\Support;

class Ground extends DoNotCreate {

    /**
     * @var bool
     */
    protected static $isConsole;

    /**
     * Checks if running in CLI mode
     *
     * @return boolean
     */
    public static function isConsole() {
        return static::$isConsole ?? (static::$isConsole = PHP_SAPI === 'cli');
    }

    /**
     * Returns readable description of value
     *
     * @param mixed $value
     * @param string $stringBorder
     * @return string
     */
    public static function valueInfo($value, string $stringBorder = '') {
        // Object
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            $items = [];

            foreach (get_object_vars($value) as $subkey => $subvalue) {
                $items[] = static::valueInfo($subkey) . ': ' . str_replace("\n", "\n    ", static::valueInfo($subvalue, '"'));
            }

            return $items ? get_class($value) . " {\n    " . implode(",\n    ", $items) . "\n}" : '{}';
        }

        // Array
        if (is_array($value)) {
            $items = [];

            foreach ($value as $subkey => $subvalue) {
                $items[] = static::valueInfo($subkey, '"') . ' => ' . str_replace("\n", "\n    ", static::valueInfo($subvalue, '"'));
            }

            return $items ? "[\n    " . implode(",\n    ", $items) . "\n]" : '[]';
        }

        // Null
        if (is_null($value)) {
            return 'NULL';
        }

        // Bool
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // Int, float
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        // String
        $replaces = ['\n' => '\\n', '\t' => '\\t', "\n" => '\n', "\t" => '\t'];

        if ($stringBorder) {
            $replaces[$stringBorder] = "\\$stringBorder";
        }

        return $stringBorder . strtr($value, $replaces) . $stringBorder;
    }
}