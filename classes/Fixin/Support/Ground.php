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
    public static function arrayInfo(array $var, string $opening = '[', string $closing = ']', $color = '#444'): string {
        $rowTemplate = "<span style=\"color: $color\">%s</span> %s";
        $items = [];

        foreach ($var as $key => $value) {
            if (is_scalar($value) && stripos($key, 'password') !== false) {
                $value = '*****';
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
            return static::arrayInfo($var->__debugInfo(), $opening, '}', '#754');
        }

        if (method_exists($var, '__toString')) {
            return $opening . static::scalarValueInfo((string) $var) . '}';
        }

        return static::arrayInfo((array) $var, $opening, '}', '#754');
    }

    /**
     * Return readable description of scalar value
     *
     * @param mixed $var
     * @return string
     */
    public static function scalarValueInfo($var): string {
        switch (gettype($var)) {
            case 'int':
                $color = '080';
                break;

            case 'float':
                $color = 'c60';
                break;

            case 'bool':
                $color = '0c0';
                $var = $var ? 'true' : 'false';
                break;

            default:
                $color = 'c00';
                $var = '"' . htmlspecialchars(strtr((string) $var, ['"' => '\"', '\n' => '\\n', '\t' => '\\t', "\n" => '\n', "\t" => '\t'])) . '"';
        }

        // String
        return "<span style=\"color: #$color\">$var</span>";
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