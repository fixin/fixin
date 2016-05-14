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
            $opening = get_class($var) . ' {';

            if (method_exists($var, '__debugInfo')) {
                $var = $var->__debugInfo();
            }
            elseif (method_exists($var, '__toString')) {
                return $opening . static::scalarValueInfo((string) $var) . '}';
            }
            else {
                $var = get_object_vars($var);
            }

            $closing = '}';
        }

        // Array
        if (is_array($var)) {
            $rowTemplate = isset($closing) ? '%s %s' : '<span style="color: #567">%s</span> %s';
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

            return ($opening ?? '[')
                . ($items ? "\n    " . implode(",\n    ", $items) . "\n" : '')
                . ($closing ?? ']');
        }

        // Null
        if (is_null($var)) {
            return '<span style="color: #60c">NULL</span>';
        }

        return static::scalarValueInfo($var);
    }
}