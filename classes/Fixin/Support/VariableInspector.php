<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class VariableInspector extends DoNotCreate {

    /**
     * @var array
     */
    static protected $scalarValueColors = [
        'integer' => '#080',
        'double' => '#c60',
        'boolean' => '#0c0'
    ];

    /**
     * @var string
     */
    static protected $valueTemplate = '<span style="color: %s">%s</span>';

    /**
     * Return array info
     *
     * @param array $var
     * @return string
     */
    public static function arrayInfo(array $var): string {
        return '[' . static::itemsInfo($var, '#754') . ']';
    }

    /**
     * Return list info
     *
     * @param array $var
     * @param string $color
     * @return string
     */
    protected static function itemsInfo(array $var, string $color): string {
        if (!$var) {
            return '';
        }

        $info = '';

        foreach ($var as $key => $value) {
            if (is_scalar($value) && stripos($key, 'password') !== false) {
                $value = '*****';
            }

            $info .= "\n    " . sprintf(static::$valueTemplate, $color, htmlspecialchars(str_pad($key, 30))) . str_replace("\n", "\n    ", static::valueInfo($value));
        }

        return $info . "\n";
    }

    /**
     * Return object info
     *
     * @param object $var
     * @return string
     */
    public static function objectInfo($var): string {
        $opening = get_class($var) . ' {';

        if (method_exists($var, '__debugInfo')) {
            $var = $var->__debugInfo();
        }
        elseif (method_exists($var, '__toString')) {
            return $opening . static::scalarInfo((string) $var) . '}';
        }
        else {
            $var = (array) $var;
        }

        return $opening . static::itemsInfo($var, '#444') . '}';
    }

    /**
     * Return scalar info
     *
     * @param int|float|string|bool $var
     * @return string
     */
    public static function scalarInfo($var): string {
        $type = gettype($var);

        if (isset(static::$scalarValueColors[$type])) {
            $color = static::$scalarValueColors[$type];

            if (is_bool($var)) {
                $var = $var ? 'true' : 'false';
            }
        }
        else {
            $var = '"' . htmlspecialchars(strtr((string) $var, ['"' => '\"', '\n' => '\\n', '\t' => '\\t', "\n" => '\n', "\t" => '\t'])) . '"';
        }

        return sprintf(static::$valueTemplate, $color ?? '#c00', $var);
    }

    /**
     * Return expression info
     *
     * @param mixed $expression
     * @return string
     */
    public static function valueInfo($expression): string {
        // Object
        if (is_object($expression)) {
            return static::objectInfo($expression);
        }

        // Array
        if (is_array($expression)) {
            return static::arrayInfo($expression);
        }

        // Null
        if (is_null($expression)) {
            return sprintf(static::$valueTemplate, '#60c', 'NULL');
        }

        return static::scalarInfo($expression);
    }
}