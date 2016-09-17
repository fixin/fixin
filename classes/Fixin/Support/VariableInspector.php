<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

use Fixin\Resource\ResourceManagerInterface;

class VariableInspector extends DoNotCreate {

    const SCALAR_VALUE_COLORS = [
        'integer' => '#080',
        'double' => '#c60',
        'boolean' => '#0ac'
    ];

    const VALUE_TEMPLATE = '<span style="color: %s">%s</span>';

    /**
     * Return array info
     *
     * @param array $var
     * @return string
     */
    public static function arrayInfo(array $var): string {
        return '[' . ($var ? "\n" . static::itemsInfo($var, '#754') : '') . ']';
    }

    /**
     * Is hidden data
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected static function isHidden(string $key, $value): bool {
        return is_scalar($value) && stripos($key, 'password') !== false;
    }

    /**
     * Return list info
     *
     * @param array $var
     * @param string $color
     * @return string
     */
    public static function itemsInfo(array $var, string $color): string {
        $result = PHP_EOL;

        foreach ($var as $key => $value) {
            if ($value instanceof ResourceManagerInterface) {
                $result .= "    " . sprintf(static::VALUE_TEMPLATE, $color, htmlspecialchars(str_pad($key, 30))) . ' ' . get_class($value) . PHP_EOL;

                continue;
            }

            if (static::isHidden($key, $value)) {
                $value = '*****';
            }

            $result .= "    " . sprintf(static::VALUE_TEMPLATE, $color, htmlspecialchars(str_pad($key, 30))) . ' ' . str_replace(PHP_EOL, PHP_EOL . '    ', static::valueInfo($value)) . PHP_EOL;
        };

        return $result;
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
            return $opening . ($var ? "\n" . static::itemsInfo($var, '#444') : '') . '}';
        }
        elseif (method_exists($var, '__toString')) {
            return $opening . htmlspecialchars((string) $var) . '}';
        }

        return $opening . '}';
    }

    /**
     * Return scalar info
     *
     * @param int|float|string|bool $var
     * @return string
     */
    public static function scalarInfo($var): string {
        $type = gettype($var);

        if (isset(static::SCALAR_VALUE_COLORS[$type])) {
            $color = static::SCALAR_VALUE_COLORS[$type];

            if (is_bool($var)) {
                $var = $var ? 'true' : 'false';
            }

            return sprintf(static::VALUE_TEMPLATE, $color, $var);
        }

        return sprintf(static::VALUE_TEMPLATE, '#c00', '"' . htmlspecialchars(strtr((string) $var, ['"' => '\"', '\n' => '\\n', '\t' => '\\t', "\n" => '\n', "\t" => '\t'])) . '"');
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
            return sprintf(static::VALUE_TEMPLATE, '#60c', 'NULL');
        }

        return static::scalarInfo($expression);
    }
}