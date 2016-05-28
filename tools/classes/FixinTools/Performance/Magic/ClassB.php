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
class ClassB extends ClassA {
    public function __get(string $name) {
        return $this->$name = $this->helpers[$name] ?? null;
    }
}