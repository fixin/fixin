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
class ClassA {

    protected $helpers = [];

    /**
     * Helper
     */
    public function __construct() {
        $this->helpers['escapeHtml'] = function($text) {
            return htmlspecialchars($text);
        };
    }

    /**
     * Non-accessible method
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call(string $name, $args) {
        if (isset($this->helpers[$name])) {
            return call_user_func_array($this->helpers[$name], $args);
        }

        return null;
    }

    /**
     * Non-accessible property
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
        return $this->helpers[$name] ?? null;
    }
}