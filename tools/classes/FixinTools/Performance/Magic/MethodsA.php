<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\Performance\Magic;

/**
 * @method escapeHtml($string)
 * @property callable escapeHtml
 */
class MethodsA
{
    protected $helpers = [];

    /**
     * Helper
     */
    public function __construct()
    {
        $this->helpers['escapeHtml'] = function($text) {
            return htmlspecialchars($text);
        };
    }

    /**
     * Non-accessible method
     */
    public function __call(string $name, $args)
    {
        if (isset($this->helpers[$name])) {
            return call_user_func_array($this->helpers[$name], $args);
        }

        return null;
    }

    /**
     * Non-accessible property
     */
    public function __get(string $name)
    {
        return $this->helpers[$name] ?? null;
    }

    /**
     * Existing method
     */
    public function existing($value)
    {
        $this->helpers['escapeHtml']($value);
    }
}