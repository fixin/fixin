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
     * Return readable description of array
     *
     * @param array $var
     * @param string $opening
     * @param string $closing
     * @return string
     */
    public static function arrayInfo(array $var, string $opening = '[', string $closing = ']'): string {
        $rowTemplate = $closing === ']' ? '<span style="color: #754">%s</span> %s' : '%s %s';
        $items = [];

        foreach ($var as $key => $value) {
            if (stripos($key, 'password') !== false) {
                if (is_array($value)) {
                    $value = array_fill_keys(array_keys($value), '*****');
                }
                elseif (is_scalar($value)) {
                    $value = '*****';
                }
            }

            $items[] = sprintf($rowTemplate, str_pad(htmlspecialchars($key), 30), str_replace("\n", "\n    ", static::valueInfo($value)));
        }

        return $opening . rtrim("\n    " . implode(",\n    ", $items)) . "\n" . $closing;
    }

    /**
     * Check if running in CLI mode
     *
     * @return boolean
     */
    public static function isConsole() {
        return static::$isConsole ?? (static::$isConsole = PHP_SAPI === 'cli');
    }

    /**
     * Return readable description of object
     *
     * @param mixed $var
     * @return string
     */
    public static function objectValueInfo($var): string {
        $opening = get_class($var) . ' {';

        if (method_exists($var, '__debugInfo')) {
            return static::arrayInfo($var->__debugInfo(), $opening, '}');
        }

        if (method_exists($var, '__toString')) {
            return $opening . static::scalarValueInfo((string) $var) . '}';
        }

        return static::arrayInfo((array) $var, $opening, '}');
    }

    /**
     * Return readable description of scalar value
     *
     * @param mixed $var
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
        return '<span style="color: #c00">"' . htmlspecialchars(strtr((string) $var, ['"' => '\"', '\n' => '\\n', '\t' => '\\t', "\n" => '\n', "\t" => '\t'])) . '"</span>';
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
            return static::objectValueInfo($var);
        }

        // Array
        if (is_array($var)) {
            return static::arrayInfo($var);
        }

        // Null
        if (is_null($var)) {
            return '<span style="color: #60c">NULL</span>';
        }

        return static::scalarValueInfo($var);
    }
}