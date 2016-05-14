<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Ground extends DoNotCreate {

    /**
     * @var bool
     */
    protected static $isConsole;

    /**
     * Check if running in CLI mode
     *
     * @return boolean
     */
    public static function isConsole() {
        return static::$isConsole ?? (static::$isConsole = PHP_SAPI === 'cli');
    }

    /**
     * Return readable description of scalar value
     *
     * @param mixed $var
     * @param bool $isValue
     * @return string
     */
    public static function scalarValueInfo($var): string {
        // Int
        if (is_int($var)) {
            return '<span style="color: #080">' . $var . '</span>';
        }

        // Float
        if (is_float($var)) {
            return '<span style="color: #c60">' . $var . '</span>';
        }

        // Bool
        if (is_bool($var)) {
            return '<span style="color: #0c0">' . ($var ? 'true' : 'false') . '</span>';
        }

        // String
        return '<span style="color: #c00">"' . htmlspecialchars(strtr($var, ['"' => '\"', '\n' => '\\n', '\t' => '\\t', "\n" => '\n', "\t" => '\t'])) . '"</span>';
    }

    /**
     * Return readable description of value
     *
     * @param mixed $var
     * @return string
     */
    public static function valueInfo($var): string {
        // Object
        if (is_object($var)) {
            $opening = get_class($var) . ' {';
            $closing = '}';
            $assigner = ': ';
            $isArray = false;

            $var = method_exists($var, '__debugInfo') ? $var->__debugInfo() : get_object_vars($var);
        }

        // Array
        if (is_array($var)) {
            $assigner = $assigner ?? '';
            $isArray = $isArray ?? true;

            $items = [];

            foreach ($var as $key => $value) {
                $items[] = str_pad(htmlspecialchars($key . $assigner), 30) . ' ' . str_replace("\n", "\n    ", static::valueInfo($value));
            }

            return '<span style="font-weight: bold">' . ($opening ?? '[') . '</span>'
                . ($items ? "\n    " . implode(",\n    ", $items) . "\n" : '')
                . '<span style="font-weight: bold">' . ($closing ?? ']') . '</span>';
        }

        // Null
        if (is_null($var)) {
            return '<span style="color: #60c">NULL</span>';
        }

        return static::scalarValueInfo($var);
    }
}