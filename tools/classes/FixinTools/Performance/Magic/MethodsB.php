<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\Performance\Magic;

/**
 * @property callable escapeHtml
 */
class MethodsB extends MethodsA {
    public function __get(string $name) {
        return $this->$name = $this->helpers[$name] ?? null;
    }
}