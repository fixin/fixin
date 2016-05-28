<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Tools;

/**
 * @author attilajenei
 *
 * @property callable escapeHtml
 */
class TestB extends Test {
    public function __get(string $name) {
        return $this->$name = $this->helpers[$name] ?? null;
    }
}